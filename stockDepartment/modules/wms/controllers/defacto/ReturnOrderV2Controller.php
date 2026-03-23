<?php

namespace app\modules\wms\controllers\defacto;

//use stockDepartment\modules\wms\models\ReturnForm;
use common\modules\client\models\Client;
use common\modules\returnOrder\models\ReturnOrderItemProduct;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2Manager;
use stockDepartment\modules\wms\models\defacto\ReturnForm;
use common\api\DeFactoSoapAPI;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\returnOrder\models\ReturnOrder;
use common\modules\returnOrder\models\ReturnOrderItems;
use common\modules\stock\models\Stock;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Controller;
use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;

class ReturnOrderV2Controller extends Controller
{
    public function actionIndex() // ok
    {
        $returnForm = new ReturnForm();
        $ordersArray = $returnForm->getPartyNew();
        $returnForm->client_id = Client::CLIENT_DEFACTO;

        return $this->render('index', [
            'returnForm' => $returnForm,
            'clientsArray' => Client::getActiveItems(),
            'ordersArray' => $ordersArray,
        ]);
    }
    /*
     *
     * */
    public function actionShowBoxesForOrder($id) // ok
    {   // show-boxes-for-order
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ReturnForm::getCountBoxesInParty($id);
    }

    /*
    * Validate out box barcode
    * @return JSON true or errors array
    * */
    public function actionValidateOurBoxBarcode() // Ok
    { // validate-our-box-barcode
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ReturnForm();
        $model->scenario = 'VALIDATE-OUR-BOX-BARCODE';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            return [
                'success' => '1',
            ];
        } else {
            $errors = ActiveForm::validate($model);
            return [
                'success'=>(empty($errors) ? '1' : '0'),
                'errors' => $errors
            ];
        }
    }

    /*
* Validate scanned box
* @return JSON true or errors array
* */
    public function actionValidateClientBoxBarcode() // Ok
    { // validate-client-box-barcode
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ReturnForm();
        $model->scenario = 'VALIDATE-CLIENT-BOX-BARCODE';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->setScannedStatus();
            $model->createOrUpdateInboundOrder();
            $model->createInboundOrderItemAndStock();
            return [
                'success' => '1',
                'countBoxes' => $model->getAllScannedBoxesInParty(),
            ];

        } else {
            $errors = ActiveForm::validate($model);
            return [
                'success' => (empty($errors) ? '1' : '0'),
                'errors' => $errors
            ];
        }
    }

    /*
    * Print the list of differences
    * */
    public function actionPrintListDifferences($id) // Ok
    {
        $returnOrderItems = [];
        if( $returnOrder = ReturnOrder::findOne($id)) {
            $returnOrderItems = $returnOrder->getOrderItems()->andWhere(['status'=>ReturnOrder::STATUS_NEW])->asArray()->all();
        }
        return $this->render('print/list-differences-pdf',['returnOrderItems'=>$returnOrderItems,'returnOrder'=>$returnOrder]);
    }

    /*
    *
    * */
    public function actionPrintUnallocatedList($id) // Ok
    {
        $withoutSecondaryAddressList = [];
        if( $returnOrder = ReturnOrder::findOne($id)) {
            $returnOrderItems = $returnOrder->getOrderItems()->select('client_box_barcode')->asArray()->column();
            if($returnOrderItems) {
                 $inboundOrderIDs = InboundOrder::find()->select('id')->andWhere([
                        'order_number' => $returnOrderItems,
                        'parent_order_number' => $returnOrder->order_number,
                        'client_id' => Client::CLIENT_DEFACTO,
                    ])->asArray()->column();

                $withoutSecondaryAddressList = Stock::find()->select('primary_address, inbound_client_box')
                             ->andWhere(["inbound_order_id"=>$inboundOrderIDs])
                             ->andWhere("secondary_address = ''")
                             ->asArray()->all();
            }
        }
        return $this->render('print/print-unallocated-box-pdf',['withoutSecondaryAddressList'=>$withoutSecondaryAddressList]);
    }
    /*
    *
    * */
    public function actionPrintAcceptedBox($id) // Ok
    {
        $withoutSecondaryAddressList = [];
        if( $returnOrder = ReturnOrder::findOne($id)) {
            $returnOrderItems = $returnOrder->getOrderItems()->select('client_box_barcode')->asArray()->column();
            if($returnOrderItems) {
                $inboundOrderIDs = InboundOrder::find()->select('id')->andWhere([
                    'order_number' => $returnOrderItems,
                    'parent_order_number' => $returnOrder->order_number,
                    'client_id' => Client::CLIENT_DEFACTO,
                ])->asArray()->column();

                $withoutSecondaryAddressList = Stock::find()->select('primary_address, secondary_address, inbound_client_box')
                    ->andWhere(["inbound_order_id"=>$inboundOrderIDs])
                    ->andWhere("secondary_address != ''")
                    ->asArray()->all();
            }
        }

        return $this->render('print/print-accepted-box-pdf',['items'=>$withoutSecondaryAddressList]);
    }

    /*
      * Confirm return order data
      * @return JSON true or errors array
      * */
    public function actionConfirmOrder($id) // Ok
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $errors = [];
        $messages = [];
        if( $returnOrder = ReturnOrder::findOne($id)) {
            $returnOrderItems = $returnOrder->getOrderItems()
                                    ->select('id')
                                    ->andWhere(['status'=>ReturnOrder::STATUS_SCANNED])
                                    ->asArray()->column();
            if($returnOrderItems) {
                $returnOrderItemProducts = ReturnOrderItemProduct::find()
                                            ->select('return_order_item_id, product_barcode, product_serialize_data, field_extra1, client_box_barcode, expected_qty')
                                            ->andWhere(['return_order_item_id' => $returnOrderItems])
                                            ->all();

                if($returnOrderItemProducts) {
                    foreach($returnOrderItemProducts as $item) {
                        ReturnOrderItems::updateAll(['status'=>ReturnOrder::STATUS_COMPLETE],['id'=>$item->return_order_item_id]);
                            if($returnOrder->client_id == Client::CLIENT_DEFACTO && YII_ENV == 'prod') {
                                $returnOrderItemProductPrepared[] = DeFactoSoapAPIV2Manager::preparedSendInBoundFeedBackDataReturn($item);
                                $toSendDataForAPI['InBoundFeedBackThreePLResponse'] = $returnOrderItemProductPrepared;
                                $api = new DeFactoSoapAPIV2Manager();
                                $api->SendInBoundFeedBackDataReturn($toSendDataForAPI);
//                                $api->SendInBoundFeedBackDataReturn($returnOrderItemProductPrepared);
                                file_put_contents("SendInBoundFeedBackDataReturn.log",date("Ymd")."\n".print_r($toSendDataForAPI,true)."\n",FILE_APPEND);
                                $toSendDataForAPI = [];
                                $returnOrderItemProductPrepared = [];
                            }
                        // TODO сделать нормально
                            $inbound = InboundOrder::find()->andWhere([
                                'parent_order_number'=> $returnOrder->order_number,
                                'order_number'=>$item->client_box_barcode,
                                'client_id'=>Client::CLIENT_DEFACTO,
                            ])->one();

                            if($inbound) {
                                $inbound->status = Stock::STATUS_INBOUND_CONFIRM;
                                $inbound->date_confirm = time();
                                $inbound->save(false);

                                Stock::updateAll([
                                    'status'=>Stock::STATUS_INBOUND_CONFIRM,
                                    'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
                                ],[
                                    'inbound_order_id'=>$inbound->id,
                                    'status'=>[
                                        Stock::STATUS_INBOUND_SCANNED,
                                        Stock::STATUS_INBOUND_OVER_SCANNED,
                                    ]
                                ]);
                            }
                        // TODO сделать нормально
//                        die;
                    }
                }
            }
            $returnOrder->status = ReturnOrder::STATUS_COMPLETE;
            $returnOrder->save(false);
        }

//        VarDumper::dump($returnOrderItemProductPrepared,10,true);
//        die;

        return [
            'success'=>'OK',
            'errors'=>$errors,
            'messages'=>$messages,
        ];
    }
}