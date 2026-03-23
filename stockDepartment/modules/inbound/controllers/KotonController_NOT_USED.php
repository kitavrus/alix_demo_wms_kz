<?php

namespace app\modules\inbound\controllers;

use common\modules\client\models\Client;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\store\models\Store;
use Yii;
use stockDepartment\components\Controller;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use yii\bootstrap\ActiveForm;
use yii\helpers\BaseFileHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Response;
use app\modules\inbound\models\KotonReturnForm;
use common\modules\stock\models\Stock;

//use yii\helpers\VarDumper;


class KotonController extends Controller
{
    public function actionInboundReturn()
    {
        $inboundForm = new KotonReturnForm();
        $clientsArray = Client::getActiveItems();
        $inboundForm->client_id = 21;

        return $this->render('inbound-return', [
            'inboundForm' => $inboundForm,
            'clientsArray' => $clientsArray,
        ]);
    }

    /*
     * Get inbound orders in status new and in process by client
     * @param integer client_id
     * @return JSON
     * */
    public function actionScanProductBarcode()
    {
        $errors = [];
        $items =[];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new KotonReturnForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $inboundItems = InboundOrder::find()
                       ->select(
                        'inbound_orders.id,
                         inbound_orders.order_number,
                         inbound_orders.parent_order_number,
                         inbound_order_items.product_barcode,
                         inbound_order_items.expected_qty,
                         inbound_order_items.accepted_qty,
                         inbound_orders.consignment_inbound_order_id'
                    )
                        ->joinWith('orderItems')
                        ->joinWith('parentOrder')
                        ->andWhere([
                            'inbound_orders.client_id' => $model->client_id,
                            'inbound_orders.order_type' => InboundOrder::ORDER_TYPE_RETURN,
                            'inbound_order_items.product_barcode' => $model->product_barcode,
                        ])
                        ->andWhere('inbound_order_items.expected_qty != inbound_order_items.accepted_qty')
                ->andWhere(['not in', 'inbound_orders.status', [
                    Stock::STATUS_INBOUND_COMPLETE,
                    Stock::STATUS_INBOUND_CONFIRM,
                    Stock::STATUS_INBOUND_ACCEPTED,
                ]])
                        ->asArray()
                        ->all();

            if($inboundItems){

                foreach ($inboundItems as $k => $item){
                    $inboundNumber = $item['order_number'];
                    $parentTitle = '';
                    if($cio = ConsignmentInboundOrders::findOne($item['consignment_inbound_order_id'])){
                        if($s = Store::findOne($cio->from_point_id)){
                            $parentTitle = $cio->party_number . ' -> ' . $s->getPointTitleByPattern('{city_name} {shopping_center_name} {name}');
                        }
                    }

                    $items[$k] = [
                        'id' => $item['id'],
                        'product_barcode' => $item['product_barcode'],
                        'order_number' => $inboundNumber,
                        'parent_title' => $parentTitle,
                        'expected_qty' => $item['expected_qty'],
                        'accepted_qty' => $item['accepted_qty'],
                        'difference_qty' => $item['expected_qty'] - $item['accepted_qty'],
                    ];
                }
                //VarDumper::dump($items, 10, true);die;
            }

        } else {
            $errors = $model->getErrors();
        }
        return [
            'success' => empty($errors) ? 1 : 0,
            'errors' => $errors,
            'renderData' => $this->renderPartial('_order_items', ['items' => $items])
        ];
    }

    /*
    * Get inbound orders in status new and in process by client
    * @param integer client_id
    * @return JSON
    * */
    public function actionAcceptProductQty()
    {
        $errors = [];
        $items =[];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new KotonReturnForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {


        } else {
            $errors = $model->getErrors();
        }
        return [
            'success' => empty($errors) ? 1 : 0,
            'errors' => $errors,
            'renderData' => $this->renderPartial('_order_items', ['items' => $items])
        ];
    }

}