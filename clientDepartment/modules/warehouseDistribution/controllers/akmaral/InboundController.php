<?php

namespace app\modules\warehouseDistribution\controllers\akmaral;

use app\modules\warehouseDistribution\models\AkmaralInboundForm;
use app\modules\warehouseDistribution\models\AkmaralOutboundForm;

use common\modules\client\models\Client;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\outbound\models\OutboundOrder;
use yii\helpers\VarDumper;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use Yii;
use common\modules\transportLogistics\components\TLHelper;
use clientDepartment\modules\client\components\ClientManager;
use yii\web\UploadedFile;
use yii\helpers\BaseFileHelper;
use common\components\OutboundManager;
use yii\helpers\ArrayHelper;
use common\modules\inbound\models\InboundOrder;
use common\modules\stock\models\Stock;
use yii\web\Response;

class InboundController extends \clientDepartment\components\Controller
{
    /*
      *
      *
      * */
    public function actionIndex()
    {
        $model = new AkmaralInboundForm();

        $client = ClientManager::getClientEmployeeByAuthUser();
        $client_id = $client->client_id;
        $filterWidgetOptionDataRoute = TLHelper::getStoreArrayByClientID($client_id);
//        $inboundOrderNumberList  = [];

//        $store_id =  Yii::$app->request->get('store_id','0');
        $party_id =  Yii::$app->request->get('party_id','0');

        $inboundOrderNumberList = ArrayHelper::map(ConsignmentInboundOrders::find()->where(['client_id' => $client->client_id,'status'=>Stock::STATUS_INBOUND_CREATED_ON_CLIENT_SIDE])->orderBy(['id'=>SORT_DESC])->all(),
            'id', 'party_number');

        if ($model->load(Yii::$app->request->post()) && $model->validate() ) {


            $model->file = UploadedFile::getInstances($model, 'file');
            $dirPath = 'uploads/akmaral/inbound/' . date('Ymd') . '/' . date('Hi');

            $cInOrder = ConsignmentInboundOrders::findOne($model->inbound_order_number);
            $cInOrderExpectedQty = 0;

            if ($model->file) {

                BaseFileHelper::createDirectory($dirPath);

                foreach ($model->file as $file) {

                    $fileToPath = $dirPath . '/' . $file->baseName . '.' . $file->extension;
                    $file->saveAs($fileToPath);

                    $order_number = $cInOrder->party_number;
                    $in = new InboundOrder();
                    $in->client_id = $client_id;
                    $in->consignment_inbound_order_id = $cInOrder->id;
                    $in->parent_order_number = $order_number;
                    $in->order_type = InboundOrder::ORDER_TYPE_INBOUND;
                    $in->order_number = $order_number;
                    $in->status = Stock::STATUS_INBOUND_CREATED_ON_CLIENT_SIDE;

                    $in->expected_qty = 0;
                    $in->accepted_qty = 0;
                    $in->deleted = 0;
                    $in->save(false);

                    $expectedQty = 0;
                    $parsedData = [];
                    if (($handle = fopen($fileToPath, "r")) !== FALSE) {
                        $parsedData = [];
                        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                            if (isset($parsedData[$data['1']])) {
                                $parsedData[$data['1']] = [
                                    'model' => $data['4'],
                                    'name' => $data['3'],
                                    'barcode' => $data['1'],
                                    'qty' => $parsedData[$data['1']]['qty'] + (int)preg_replace('/[^\d]+/', '', $data['7'])
                                ];
                            } else {
                                $parsedData[$data['1']] = [
                                    'model' => $data['4'],
                                    'name' => $data['3'],
                                    'barcode' => $data['1'],
                                    'qty' => (int)preg_replace('/[^\d]+/', '', $data['7'])
                                ];
                            }

                        }
                    }

                    foreach ($parsedData as $productData) {

                        $ioi = new InboundOrderItem();
                        $ioi->inbound_order_id = $in->id;
                        $ioi->product_barcode = $productData['barcode'];
                        $ioi->product_name = $productData['name'];
                        $ioi->expected_qty = $productData['qty'];
                        $ioi->status = Stock::STATUS_INBOUND_NEW;
                        $ioi->save(false);

                        $expectedQty += $ioi->expected_qty;
                        $rowCount = 0;
                        $rows = [];
                        for ($i = 1; $i <= $ioi->expected_qty; $i++) {

  /*                          $stock = new Stock();
                            $stock->client_id = $client_id;
                            $stock->inbound_order_id = $ioi->inbound_order_id;
                            $stock->product_barcode = $ioi->product_barcode;
                            $stock->product_name = $ioi->product_name;
                            $stock->status = Stock::STATUS_INBOUND_NEW;
                            $stock->status_availability = Stock::STATUS_AVAILABILITY_NO;
                            $stock->save(false);*/
                            $rows[] = [$client_id,$ioi->inbound_order_id,$ioi->product_barcode,$ioi->product_name,Stock::STATUS_INBOUND_NEW,Stock::STATUS_AVAILABILITY_NO];

                            if($rowCount == 1000) {
                                $columns = ['client_id','inbound_order_id','product_barcode','product_name','status','status_availability'];
                                Yii::$app->db->createCommand()->batchInsert(Stock::tableName(), $columns, $rows)->execute();
                                $rows = [];
                            }
                        }

                        if(!empty($rows)) {
                            $columns = ['client_id','inbound_order_id','product_barcode','product_name','status','status_availability'];
                            Yii::$app->db->createCommand()->batchInsert(Stock::tableName(), $columns, $rows)->execute();
                            $rows = [];
                        }
                    }

                    $in->expected_qty = $expectedQty;
                    $in->save(false);

                    $cInOrder->expected_qty = $expectedQty;
                    $cInOrder->save(false);

                    $party_id = $cInOrder->id;

                    Yii::$app->getSession()->setFlash('success', Yii::t('return/messages', '{n, plural, one{# файл} few{# файла} many{# файлов} other{# файлов}} успешно загружено', ['n' => count($model->file)]));

                    return $this->redirect(['index', 'party_id' => $party_id]);
                }
            }
        }

        if(!empty($party_id)) {
            $model->inbound_order_number = $party_id;
        }

        return $this->render('index',
            [
                'model' => $model,
                'filterWidgetOptionDataRoute' => $filterWidgetOptionDataRoute,
                'inboundOrderNumberList' => $inboundOrderNumberList,
            ]);
    }

    /*
     *
     *
     * */
    public function actionGetInboundOrdersNumber()
    {

        $client = ClientManager::getClientEmployeeByAuthUser();
        $count = ConsignmentInboundOrders::find()->where(['client_id' => $client->client_id])->count();

        $in = new ConsignmentInboundOrders();
        $in->client_id = $client->client_id;
        $in->status = Stock::STATUS_INBOUND_CREATED_ON_CLIENT_SIDE;
        $in->party_number = date('Ymd').'-'.$client->client_id.$count;
        $in->save(false);


        $data = ArrayHelper::map(ConsignmentInboundOrders::find()->where(['client_id' => $client->client_id,'status'=>Stock::STATUS_INBOUND_CREATED_ON_CLIENT_SIDE, 'deleted'=>ConsignmentInboundOrders::NOT_SHOW_DELETED])->orderBy(['id'=>SORT_DESC])->all(),
            'id', 'party_number');

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'message' => 'Success',
            'data_options' => $data,

        ];
    }

    /*
     *
     *
     * */
    public function actionAcceptInboundOrder()
    {
        $errors = 0;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $client = ClientManager::getClientEmployeeByAuthUser();

        $store_id = Yii::$app->request->post('store_id');
        $party_id = Yii::$app->request->post('party_id');
        $client_id = $client->client_id;

        if(InboundOrder::find()->where(['consignment_inbound_order_id' => $party_id, 'client_id' =>$client_id])->exists() ) {

            ConsignmentInboundOrders::updateAll(['status' => Stock::STATUS_INBOUND_NEW], ['id' => $party_id, 'client_id' => $client_id]);
            InboundOrder::updateAll(['status' => Stock::STATUS_INBOUND_NEW], ['consignment_inbound_order_id' => $party_id, 'client_id' => $client_id]);
            Yii::$app->getSession()->setFlash('success', Yii::t('return/messages', 'Накладная успешно принята'));
            $errors = 0;

//            $cio = ConsignmentInboundOrders::findOne(['id' => $party_id, 'client_id' => $client_id]);
//            $cio->status = Stock::STATUS_INBOUND_NEW;
//            $cio->save(false);


            //S: Create new Delivery Proposal
//            $dp = new TlDeliveryProposal();
//            $dp->route_from = $store_id;
//            $dp->route_to = 4; // DC stock Nomadex;
//            $dp->save(false);

//            $dpOrderNumber = $cio->order_number; //. ' ' . $outboundModel->order_number;

//            if ($dpOrder = TlDeliveryProposalOrders::findOne(['client_id' => $client_id, 'order_id' => $outboundModel->id, 'order_number' => $dpOrderNumber])) {
//                $dp = TlDeliveryProposal::findOne($dpOrder->tl_delivery_proposal_id);
//            } else {
//                $dp = new TlDeliveryProposal();
//                $dpOrder = new TlDeliveryProposalOrders();
//            }

//            $dp->status = TlDeliveryProposal::STATUS_NEW;
//            $dp->client_id = $outboundModel->client_id;
//            $dp->route_from = '4'; // НАШ склад
//            $dp->route_to = $outboundModel->to_point_id;
//            $dp->save(false);

            // Добавить заказы
//            $dpOrder->client_id = $dp->client_id;
//            $dpOrder->tl_delivery_proposal_id = $dp->id;
//            $dpOrder->order_id = $outboundModel->id;
//            $dpOrder->order_type = TlDeliveryProposalOrders::ORDER_TYPE_RPT;
//            $dpOrder->delivery_type = TlDeliveryProposalOrders::DELIVERY_TYPE_OUTBOUND;
//            $dpOrder->order_number = $outboundModel->parent_order_number . ' ' . $outboundModel->order_number;
//            $dpOrder->save(false);

            //E: Create new Delivery Proposal

        } else {
            Yii::$app->getSession()->setFlash('error', Yii::t('return/messages', 'Накладная не принята, нужно добавить хотябы один короб'));
            $errors = 1;
        }

        $data = ArrayHelper::map(ConsignmentInboundOrders::find()->where(['client_id' => $client->client_id,'from_point_id'=>$store_id,'status'=>Stock::STATUS_INBOUND_CREATED_ON_CLIENT_SIDE])->orderBy(['id'=>SORT_DESC])->all(),
            'id', 'party_number');

        return [
            'errors' => $errors,
            'message' => 'Success',
            'data_options' => $data,
            'store_id' => $store_id,
            'party_id' => $party_id,
        ];
    }

    /*
     *
     * */
    public function actionGetOrdersItemsByPartyId()
    {
        $client = ClientManager::getClientEmployeeByAuthUser();

        $party_id = Yii::$app->request->post('party_id');

        $items = InboundOrder::find()->where([
                'status'=>Stock::STATUS_INBOUND_CREATED_ON_CLIENT_SIDE,
                'consignment_inbound_order_id'=>$party_id,
                'client_id' => $client->client_id,
                'deleted' => Stock::NOT_SHOW_DELETED,
            ]
        )->asArray()->all();

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'message' => 'Success',
            'items' =>$this->renderPartial('_order_items',['items'=>$items])
        ];
    }

    /*Удаляет ConsignmentInboundOrders и все связанные записи
     *
     * */
    public function actionDeleteInboundOrder()
    {
        $client = ClientManager::getClientEmployeeByAuthUser();
        //Yii::$app->session->getAllFlashes(true);
        $party_id = Yii::$app->request->post('party_id');
        if($conInboundOrder = ConsignmentInboundOrders::findOne(['id'=>$party_id, 'client_id'=>$client->client_id])){
            if($inboundOrders = $conInboundOrder->getInboundOrders()->andWhere(['client_id' => $client->client_id])->all()){
                foreach($inboundOrders as $inboundOrder){
                    if($inboundOI = $inboundOrder->orderItems){
                        foreach ($inboundOI as $io){
                            $io->deleted = ConsignmentInboundOrders::SHOW_DELETED;
                            $io->save(false);
                        }
                    }
                    $inboundOrder->deleted = ConsignmentInboundOrders::SHOW_DELETED;
                    $inboundOrder->save(false);
                }

            }

            $conInboundOrder->deleted = ConsignmentInboundOrders::SHOW_DELETED;
            $conInboundOrder->save(false);
            Yii::$app->session->setFlash('success', Yii::t('return/messages', 'Накладная № {0} была удалена', [$conInboundOrder->party_number]));
        }

        return $this->redirect('index');
    }


    /*Удаляет inbound order
     *
     * */
    public function actionDeleteInboundOrderItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $client = ClientManager::getClientEmployeeByAuthUser();
        //Yii::$app->session->getAllFlashes(true);
        $item_id = Yii::$app->request->post('item_id');
        if ($InboundOrder = InboundOrder::findOne(['id' => $item_id, 'client_id' => $client->client_id])) {
            $InboundOrder->deleted = ConsignmentInboundOrders::SHOW_DELETED;
            $InboundOrder->save(false);
            Yii::$app->session->setFlash('success',
                Yii::t('return/messages', 'Короб № {0} был удален', [$InboundOrder->order_number]));
            return [
                'message' => 'Success',
                'item_id' => $InboundOrder->id,
            ];
        }

        return [
            'message' => 'Error',
            'item_id' =>''
        ];
    }


}