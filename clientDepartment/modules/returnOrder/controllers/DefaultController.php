<?php

namespace app\modules\returnOrder\controllers;

use app\modules\returnOrder\models\ReturnForm;

use bossDepartment\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;
//use yii\web\Controller;
use Yii;
use common\modules\transportLogistics\components\TLHelper;
use clientDepartment\modules\client\components\ClientManager;
use yii\web\UploadedFile;
use yii\helpers\BaseFileHelper;
use yii\helpers\ArrayHelper;
use common\modules\inbound\models\InboundOrder;
use common\modules\stock\models\Stock;
use yii\web\Response;

class DefaultController extends \clientDepartment\components\Controller
{
    /*
     *
     *
     * */
    public function actionIndex()
    {
        $model = new ReturnForm();

        $client = ClientManager::getClientEmployeeByAuthUser();
        $client_id = $client->client_id;
        $filterWidgetOptionDataRoute = TLHelper::getStoreArrayByClientID($client_id);
//        $inboundOrderNumberList  = [];

        $store_id =  Yii::$app->request->get('store_id','0');
        $party_id =  Yii::$app->request->get('party_id','0');

        $inboundOrderNumberList = ArrayHelper::map(ConsignmentInboundOrders::find()->where(['client_id' => $client->client_id,'from_point_id'=>$store_id,'status'=>Stock::STATUS_INBOUND_CREATED_ON_CLIENT_SIDE])->orderBy(['id'=>SORT_DESC])->all(),
            'id', 'party_number');

        if ($model->load(Yii::$app->request->post()) && $model->validate() ) {



            $model->file = UploadedFile::getInstances($model, 'file');
            $dirPath = 'uploads/cotton/inbound/' . date('Ymd') . '/' . date('Hi');

            $cInOrder = ConsignmentInboundOrders::findOne($model->inbound_order_number);
            $cInOrderExpectedQty = 0;

            if ($model->file) {

                BaseFileHelper::createDirectory($dirPath);

                foreach ($model->file as $file) {
                    $fileToPath = $dirPath . '/' . $file->baseName . '.' . $file->extension;
                    $file->saveAs($fileToPath);

                    $order_number = trim($file->baseName);
                    $inOrderExpectedQty = 0;


                    $inIDs = InboundOrder::find()->select('id')->where([
                        'client_id'=>$client_id,
                        'consignment_inbound_order_id'=>$cInOrder->id,
                        'from_point_id'=>$model->store_id,
                        'order_number'=>$order_number,
                    ])->column();

                    Stock::deleteAll(['client_id'=>$client_id,'inbound_order_id'=>$inIDs]);
                    InboundOrderItem::deleteAll(['inbound_order_id'=>$inIDs]);

                    InboundOrder::deleteAll(['id'=>$inIDs]);

                    if(!($in = InboundOrder::find()->where([
                        'client_id'=>$client_id,
                        'consignment_inbound_order_id'=>$cInOrder->id,
                        'from_point_id'=>$model->store_id,
                        'order_number'=>$order_number,
                    ])->one())) {

                        $in = new InboundOrder();
                        $in->client_id = $client_id;
                        $in->consignment_inbound_order_id = $cInOrder->id;
                        $in->parent_order_number = $cInOrder->party_number;
                        $in->order_type = InboundOrder::ORDER_TYPE_RETURN;
                        $in->order_number = $order_number;
                        $in->from_point_id = $model->store_id;
                        $in->status = Stock::STATUS_INBOUND_CREATED_ON_CLIENT_SIDE;

                    }

                    $in->expected_qty = 0;
                    $in->accepted_qty = 0;
                    $in->deleted = 0;
                    $in->save(false);

//                    Stock::deleteAll(['client_id'=>$client_id,'inbound_order_id'=>$in->id]);
//                    Stock::deleteAll(['client_id'=>$client_id,'inbound_order_id'=>$inIDs]);
//                    InboundOrderItem::deleteAll(['inbound_order_id'=>$inIDs]);

                    if ( ($handle = fopen($fileToPath, "r")) !== FALSE ) {
                        while (($data = fgetcsv($handle, 1000, " ")) !== FALSE) {

                            $data = array_filter($data, 'trim');
                            $line = [];

                            foreach($data as $v) {
                                $line[] = $v;
                            }

                            file_put_contents('return-inbound-upload-file.log',"\n"."\n",FILE_APPEND);
                            file_put_contents('return-inbound-upload-file.log',$order_number."\n",FILE_APPEND);
                            file_put_contents('return-inbound-upload-file.log',print_r($data,true)."\n",FILE_APPEND);

//                            $pb = isset($line['0']) ? $line['0'] : 0;

//                            file_put_contents('return-inbound-upload-file.log',"COUNt line: ".print_r(count($line),true)."\n",FILE_APPEND);
//                            file_put_contents('return-inbound-upload-file.log',"len line 0 : ".strlen($line['0'])."\n",FILE_APPEND);



                            if(!empty($line) && count($line) < 4 && strlen($line['0']) == 13) {

                                array_unshift($line,'0','1','2');
                            }

//                            file_put_contents('return-inbound-upload-file.log',"\n"."\n",FILE_APPEND);
                            file_put_contents('return-inbound-upload-file.log',print_r($line,true),FILE_APPEND);
                            file_put_contents('return-inbound-upload-file.log',"\n".'------------'."\n",FILE_APPEND);

                            if( (!empty($line) && count($line) == 4) OR (!empty($line) && count($line) == 1 && strlen($line['0']) == 13)) {

                                if(count($line) == 4) {
                                    $product_barcode = $line[3];
                                }else {
                                    $product_barcode = $line[0];
                                }

                                if (!($inItem = InboundOrderItem::find()->where([
                                    'inbound_order_id' => $in->id,
                                    'product_barcode' => $product_barcode,
                                ])->one())
                                ) {
                                    $inItem = new InboundOrderItem();
                                    $inItem->inbound_order_id = $in->id;
                                    $inItem->product_barcode = $product_barcode;

                                    $inItem->status = Stock::STATUS_INBOUND_CREATED_ON_CLIENT_SIDE;
                                    $inItem->expected_qty = 0;
                                    $inItem->accepted_qty = 0;
                                    $inItem->box_barcode = $order_number;
                                }

                                $inItem->expected_qty += 1;
                                $inItem->deleted = 0;
                                $inItem->save(false);

                                $inOrderExpectedQty += 1;

                                $stock = new Stock();
                                $stock->client_id = $client_id;
                                $stock->inbound_order_id = $inItem->inbound_order_id;
                                $stock->product_barcode = $inItem->product_barcode;
                                $stock->product_model = $inItem->product_model;
                                $stock->status = Stock::STATUS_INBOUND_NEW;
                                $stock->status_availability = Stock::STATUS_AVAILABILITY_NO;
                                $stock->deleted = 0;
                                $stock->save(false);
                            }
                        }

                        fclose($handle);

                        $in->expected_qty = $inOrderExpectedQty;
                        $in->save(false);
                    }
                    unset($in,$handle,$stock,$inItem);

                    $cInOrderExpectedQty += $inOrderExpectedQty;
                }

                $cInOrder->expected_qty = $cInOrderExpectedQty;
                $cInOrder->save(false);
                $store_id = $model->store_id;
                $party_id = $cInOrder->id;
            }

            Yii::$app->getSession()->setFlash('success', Yii::t('return/messages', '{n, plural, one{# файл} few{# файла} many{# файлов} other{# файлов}} успешно загружено', ['n' => count($model->file)]));

            return $this->redirect(['index','store_id'=>$store_id,'party_id'=>$party_id]);
        }


        if(!empty($store_id) && !empty($party_id)) {
            $model->store_id = $store_id;
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
        $store_id = Yii::$app->request->post('store_id');
        $generate_new = Yii::$app->request->post('generate_new');

        $client = ClientManager::getClientEmployeeByAuthUser();
        $count = ConsignmentInboundOrders::find()->where(['client_id' => $client->client_id])->count();

        if(!empty($store_id) &&
           // ( !ConsignmentInboundOrders::find()->where(['client_id' => $client->client_id,'from_point_id'=>$store_id,'status'=>Stock::STATUS_INBOUND_CREATED_ON_CLIENT_SIDE])->exists() || !empty($generate_new) )
             !empty($generate_new)
        ) {

            $in = new ConsignmentInboundOrders();
            $in->client_id = $client->client_id;
            $in->from_point_id = $store_id;
            $in->status = Stock::STATUS_INBOUND_CREATED_ON_CLIENT_SIDE;
            $in->party_number = date('Ymd').'-'.$store_id.'-'.$client->client_id.$count;
            $in->save(false);

        }

        //$data[0] = Yii::t('return/titles', 'Select order');
        $data = ArrayHelper::map(ConsignmentInboundOrders::find()->where(['client_id' => $client->client_id,'from_point_id'=>$store_id,'status'=>Stock::STATUS_INBOUND_CREATED_ON_CLIENT_SIDE, 'deleted'=>ConsignmentInboundOrders::NOT_SHOW_DELETED])->orderBy(['id'=>SORT_DESC])->all(),
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