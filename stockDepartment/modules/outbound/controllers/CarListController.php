<?php

namespace app\modules\outbound\controllers;

use common\modules\crossDock\models\CrossDock;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use common\modules\outbound\models\OutboundOrder;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use Yii;
use common\modules\client\models\Client;
use stockDepartment\components\Controller;
use stockDepartment\modules\product\models\ProductSearch;
use common\modules\transportLogistics\components\TLHelper;
use common\components\OutboundManager;
use stockDepartment\modules\outbound\models\OutboundOrderItemSearch;
use common\modules\inbound\models\InboundOrder;
use yii\data\ArrayDataProvider;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use stockDepartment\modules\outbound\models\OutboundCarListSearch;
use common\components\DeliveryProposalManager;
use stockDepartment\modules\crossDock\models\CrossDockSearch;
use common\modules\stock\models\Stock;
use stockDepartment\modules\crossDock\models\CrossDockItemSearch;


class CarListController extends Controller
{
    /*
     *
     * */
    public function actionIndex()
    {
        $searchModel = new OutboundCarListSearch();
        $crossDockSearch = new CrossDockSearch();
        $crossDockDataProvider = $crossDockSearch->search(Yii::$app->request->queryParams);
        $crossDockDataProvider->query->andWhere(['status' => Stock::STATUS_CROSS_DOCK_COMPLETE]);
        $outboundDataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $outboundDataProvider->query->andWhere(['in', 'status', [
            Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API,
            Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
            Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
        ]]);

        $clientsArray = Client::getActiveItems();
        $arrayDataProvider = new ArrayDataProvider([
            'allModels' => array_merge($outboundDataProvider->query->asArray()->all(), $crossDockDataProvider->query->asArray()->all()),
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'attributes' => [
                    'id'
                ],
            ],

        ]);
        $arrayDataProvider->sort->defaultOrder = ['id'=>'ASC'];

        return $this->render('index', [
            'dataProvider' => $arrayDataProvider,
            'clientsArray' => $clientsArray,
        ]);
    }

    /*
     * Select order for print pick list
     *
     * */
    public function actionCreate()
    {
        $clientsArray = Client::getActiveItems();
        $storeArray = TLHelper::getStockPointArray();
        $model = new OutboundOrder();
        $model->scenario = 'manual-create';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $count = ConsignmentOutboundOrder::find()->andWhere(['client_id' => $model->client_id])->count();
            $orderCount = OutboundOrder::find()->andWhere(['client_id' => $model->client_id])->count();
            $partyNumber = date('Ymd') . '-' . $model->client_id . '-' . $count;
            $orderNumber = '[dop]-' . date('Ymd') . '-' . $model->client_id . '-' . $orderCount;

            $oManager = new OutboundManager();
            $oManager->initBaseData($model->client_id, $partyNumber, $orderNumber);
            if ($coo = $oManager->createUpdateConsignmentOutbound()) {
                $data = [
                    'consignment_outbound_order_id' => $coo->id,
                    'parent_order_number' => $coo->party_number,
                    'order_number' => $orderNumber,
                    'from_point_id' => $model->from_point_id,
                    'to_point_id' => $model->to_point_id,
                    'mc' => $model->mc,
                    'kg' => $model->kg,
                    'title' => $model->title,
                    'description' => $model->description,
                    'accepted_number_places_qty' => $model->accepted_number_places_qty,
                    'expected_number_places_qty' => $model->accepted_number_places_qty,
                ];

               $oo = $oManager->createUpdateOutbound($data);
                $oo->status = Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL;
                $oo->save(false);
               $oManager->createUpdateDeliveryProposalAndOrder();
               return $this->redirect(['/outbound/car-list/view', 'id' => $oo->id, 'print-pdf' => 1, 'order_type' => TlDeliveryProposalOrders::ORDER_TYPE_RPT]);

                }
            }


        return $this->render('create', [
            'clientsArray' => $clientsArray,
            'storeArray' => $storeArray,
            'model' => $model,
        ]);
    }

    /*
     * View outbound order
     * @param integer Outbound order id
     * @return render view html
     * */
    public function actionView($id, $order_type)
    {
        $view = 'view';
        if ($order_type == TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK) {

            $model = $this->findCrossDockModel($id);
            $itemSearch = new CrossDockItemSearch();
            $ItemsProvider = $itemSearch->search(Yii::$app->request->queryParams);
            $ItemsProvider->query->andWhere(['cross_dock_id' => $model->id]);
            $view = 'cd-view';
        } else {
            $model = $this->findModel($id);
            $itemSearch = new OutboundOrderItemSearch();
            $ItemsProvider = $itemSearch->search(Yii::$app->request->queryParams);
            $ItemsProvider->query->andWhere(['outbound_order_id' => $model->id]);
            $view = 'view';
        }
        $stock = new Stock();
        $statusArray = $stock->getStatusArray();
        $clientStoreArray = TLHelper::getStoreArrayByClientID($model->client_id);
        $clientsArray = Client::getActiveItems();

        return $this->render($view, [
            'model' => $model,
            'ItemsProvider' => $ItemsProvider,
            'searchModel' => $itemSearch,
            'clientsArray' => $clientsArray,
            'statusArray' => $statusArray,
            'clientStoreArray' => $clientStoreArray,
        ]);
    }

    /*
 * View outbound order
 * @param integer Outbound order id
 * @return render view html
 * */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'manual-update';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $oManager = new OutboundManager();
            $oManager->initBaseData($model->client_id, $model->parent_order_number, $model->order_number);

            $data = [
                'consignment_outbound_order_id' => $model->consignment_outbound_order_id,
                'parent_order_number' => $model->parent_order_number,
                'order_number' => $model->order_number,
                'from_point_id' => $model->from_point_id,
                'to_point_id' => $model->to_point_id,
                'kg' => $model->kg,
                'mc' => $model->mc,
                'accepted_number_places_qty' => $model->accepted_number_places_qty,
                'expected_number_places_qty' => $model->accepted_number_places_qty,
                'title' => $model->title,
                'description' => $model->description,
            ];

            $oManager->createUpdateOutbound($data);
            $oManager->createUpdateDeliveryProposalAndOrder();


        return $this->redirect(['/outbound/car-list/view', 'id' => $model->id]);
    }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /*
     * Print Label Barcode
     * @param integer Outbound order id
     * @return download PDF Label Barcode
     * */
    public function actionPrintLabelBarcode($id, $order_type)
    {

        if($order_type == TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK){
            $oo =  $this->findCrossDockModel($id);
        } else {
            $oo =  $this->findModel($id);
        }
        $deliveryOrder = TlDeliveryProposalOrders::find()->andWhere([
            'client_id' => $oo->client_id,
            'order_id' => $oo->id,
            'order_type' => $order_type,
        ])->one();

        $boxes = [];
        $labelCount = $deliveryOrder->number_places_actual;
        $order_number = $deliveryOrder->order_number;

        if($deliveryOrder->order_type == TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK)
        {
            $labelCount = 1;
            if(!$oo->order_number){
                $order_number = '[c-d-'.$oo->party_number.'-'.$oo->id.']';
            }


        }
        //VarDumper::dump($oo->order_number, 10, true); die;
        $dp = TlDeliveryProposal::findOne($deliveryOrder->tl_delivery_proposal_id);
        for ($i = 0; $i < $labelCount; $i++){
            $boxes[] = [
                'order_number' => $order_number
            ];
        }

        return $this->render('_box-label-pdf', ['boxes' => $boxes, 'model' => $dp,'outboundOrderModel'=>$oo]);
    }

    /**
     * Finds the OutboundOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OutboundOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OutboundOrder::findOne(['id' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the OutboundOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OutboundOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCrossDockModel($id)
    {
        if (($model = CrossDock::findOne(['id' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}