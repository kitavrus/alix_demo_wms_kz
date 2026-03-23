<?php

namespace app\modules\outbound\controllers;

use app\modules\outbound\controllers\repository\ReturnToOrderRepository;
use app\modules\outbound\models\OutboundBoxSearch;
use common\modules\client\models\Client;
use common\modules\outbound\models\OutboundOrder;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\components\TLHelper;
use stockDepartment\components\Controller;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;

class ReturnToOrderController extends Controller
{
    /*
     * Index
     * */
    public function actionIndex()
    {
        $stock = new Stock();
        $searchModel = new OutboundBoxSearch();
        $dataProvider = $searchModel->searchArray(Yii::$app->request->queryParams);

        $clientsArray = Client::getActiveWMSItems();
        $storeArray = TLHelper::getStoreArrayByClientID();
        $statusArray = $stock->getStatusArray();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientsArray' => $clientsArray,
            'storeArray' => $storeArray,
            'statusArray' => $statusArray,
        ]);
    }

    public function actionView($boxBarcode, $product_barcode = null)
    {
        $boxBarcode = trim((string)$boxBarcode);
        if ($boxBarcode === '') {
            throw new NotFoundHttpException('Короб не указан');
        }

        // Товары сейчас в коробе (box_barcode совпадает)
        $inBoxQuery = Stock::find()
            ->andWhere(['box_barcode' => $boxBarcode])
            ->orderBy(['id' => SORT_ASC]);
        if ($product_barcode !== null && $product_barcode !== '') {
            $inBoxQuery->andWhere(['product_barcode' => $product_barcode]);
        }
        $inBoxStocks = $inBoxQuery->all();

        // Товары, которые вернули на склад с этого короба (берём из unreserve_snapshot)
        $returnedQuery = Stock::find()
            ->andWhere(['outbound_order_id' => 0])
            ->andWhere(['not', ['unreserve_snapshot' => null]])
            ->andWhere(['!=', 'unreserve_snapshot', ''])
            ->andWhere(['like', 'unreserve_snapshot', '"box_barcode":"' . $boxBarcode . '"'])
            ->orderBy(['id' => SORT_ASC]);
        if ($product_barcode !== null && $product_barcode !== '') {
            $returnedQuery->andWhere(['product_barcode' => $product_barcode]);
        }
        $returnedStocks = $returnedQuery->all();

        $rows = [];
        foreach ($inBoxStocks as $stock) {
            $rows[] = ['stock' => $stock, 'is_in_box' => true];
        }
        foreach ($returnedStocks as $stock) {
            $rows[] = ['stock' => $stock, 'is_in_box' => false];
        }

        $model = null;
        $orderNumber = '';
        if (!empty($inBoxStocks)) {
            $model = $inBoxStocks[0];
            if ($model->outbound_order_id) {
                $order = OutboundOrder::findOne($model->outbound_order_id);
                if ($order) {
                    $orderNumber = $order->order_number;
                }
            }
        } elseif (!empty($returnedStocks)) {
            $model = $returnedStocks[0];
            $snapshot = json_decode($model->unreserve_snapshot, true);
            if (is_array($snapshot) && !empty($snapshot['outbound_order_id'])) {
                $order = OutboundOrder::findOne($snapshot['outbound_order_id']);
                if ($order) {
                    $orderNumber = $order->order_number;
                }
            }
        }

        if ($model === null) {
            throw new NotFoundHttpException('По коробу нет данных');
        }

        $boxItemsProvider = new ArrayDataProvider([
            'allModels' => $rows,
            'pagination' => ['pageSize' => 50],
        ]);

        return $this->render('view', [
            'model' => $model,
            'boxBarcode' => $boxBarcode,
            'orderNumber' => $orderNumber,
            'boxItemsProvider' => $boxItemsProvider,
        ]);
    }

    public function actionReturnToStock($id)
    {
        $repo = new ReturnToOrderRepository();

        try {
            $repo->unreserve($id);
            Yii::$app->session->setFlash('success', 'Товар успешно возвращён на склад');
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    public function actionReturnToOrder($id)
    {
        $repo = new ReturnToOrderRepository();

        try {
            $repo->reserveBack($id);
            Yii::$app->session->setFlash('success', 'Товар успешно возвращён в заказ');
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }
}