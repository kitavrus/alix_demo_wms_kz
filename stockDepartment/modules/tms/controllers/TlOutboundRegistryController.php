<?php

namespace app\modules\tms\controllers;

use common\components\BarcodeManager;
use common\modules\transportLogistics\models\TlOutboundRegistryItems;
use stockDepartment\modules\tms\models\forms\RegistryScanningForm;
use Yii;
use common\modules\transportLogistics\models\TlOutboundRegistry;
use stockDepartment\modules\tms\models\TlOutboundRegistrySearch;
use stockDepartment\components\Controller;
use yii\web\NotFoundHttpException;
//use yii\filters\VerbFilter;
use common\modules\transportLogistics\components\TLHelper;
use yii\web\Response;
//use common\modules\codebook\models\BaseBarcode;
use common\components\DeliveryProposalManager;
//use common\modules\client\models\ClientEmployees;
//use common\helpers\DateHelper;
//use common\modules\transportLogistics\models\TlDeliveryProposal;
//use common\events\DpEvent;
//use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;
//use common\modules\transportLogistics\models\TlDeliveryProposalRouteTransport;
//use common\modules\transportLogistics\models\TlDeliveryProposalDefaultRoute;
//use common\modules\transportLogistics\models\TlDeliveryRoutes;

/**
 * TlOutboundRegistryController implements the CRUD actions for TlOutboundRegistry model.
 */
class TlOutboundRegistryController extends Controller
{

    /**
     * Lists all TlOutboundRegistry models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TlOutboundRegistrySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TlOutboundRegistry model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'storeArray' => TLHelper::getStockPointArray(),
            'formModel' => new RegistryScanningForm(),
        ]);
    }

    /**
     * Creates a new TlOutboundRegistry model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TlOutboundRegistry();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            DeliveryProposalManager::recalculateRegistryData($model->id);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TlOutboundRegistry model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            DeliveryProposalManager::recalculateRegistryData($model->id);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Finds the TlOutboundRegistry model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TlOutboundRegistry the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TlOutboundRegistry::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Add Item to existing TlOutboundRegistry model.
     * @return mixed
     */
    public function actionScanProposalBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $errors = [];


        $formModel = new RegistryScanningForm();
        if($formModel->load(Yii::$app->request->post()) && $formModel->validate()){
            $model = $this->findModel($formModel->registry_id);

            //Добавляем Items
            $dp = BarcodeManager::getDpByBaseBarcode($formModel->proposal_barcode);

            $registryItem = new TlOutboundRegistryItems();
            $registryItem->tl_delivery_proposal_id = $dp->id;
            $registryItem->tl_outbound_registry_id = $model->id;
            $registryItem->route_from = $dp->route_from;
            $registryItem->route_to = $dp->route_to;
            $registryItem->weight = $dp->kg_actual;
            $registryItem->volume = $dp->mc_actual;
            $registryItem->places = $dp->number_places_actual;
            $registryItem->extra_fields = $dp->extra_fields;
            $registryItem->save(false);

            //Добавляем к заявке на доставку маршруты по умолчанию если таковые имеются
            $dpManager = new DeliveryProposalManager(['id'=>$dp->id]);
            $dpManager->generateDefaultRoutes();
            $dpManager->onAddRegistry();

            //Пересчитываем kg, mc, places в реестре
            DeliveryProposalManager::recalculateRegistryData($model->id);
            $model = $this->findModel($formModel->registry_id);

        } else {
            $errors = $formModel->getErrors();
        }

        return [
            'success' => $errors ? 0 : 1,
            'errors' => $errors,
            'data' => [
                'grid' => isset($model) ? $this->renderPartial('_items-grid', ['model' => $model, 'storeArray' => TLHelper::getStockPointArray()]) : '',
                'kg' => isset($model) ? $model->weight : 0,
                'mc' => isset($model) ? $model->volume : 0,
                'places' => isset($model) ? $model->places : 0,
            ],
        ];
    }

    /**
     * Add Item to existing TlOutboundRegistry model.
     * @return mixed
     */
    public function actionDeleteItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $errors = [];
        $id = Yii::$app->request->post('parent-id');
        $model = $this->findModel($id);
        $itemModel = TlOutboundRegistryItems::findOne(Yii::$app->request->post('id'));

        if($model && $itemModel){
            $itemModel->deleted = TlOutboundRegistryItems::SHOW_DELETED;
            $itemModel->save(false);
            DeliveryProposalManager::recalculateRegistryData($model->id);
            $model = $this->findModel($id);

        }

        return [
            'success' => $errors ? 0 : 1,
            'errors' => $errors,
            'data' => [
                'grid' => isset($model) ? $this->renderPartial('_items-grid', ['model' => $model, 'storeArray' => TLHelper::getStockPointArray()]) : '',
                'kg' => isset($model) ? $model->weight : 0,
                'mc' => isset($model) ? $model->volume : 0,
                'places' => isset($model) ? $model->places : 0,
            ],
        ];
    }

    /**
     * Print registry PDF.
     * @return mixed
     */
    public function actionPrintRegistryPdf($id)
    {
        $model = $this->findModel($id);

        if($this->printType == 'html'){
            Yii::$app->layout = 'print-html';
            return $this->render('_print-registry-html',['model'=>$model]);
        }
        return $this->render('_print-registry-pdf', ['model' => $model]);
    }

    /**
     * Print TTN for each proposal in registry.
     * @return mixed
     */
    public function actionPrintRegistryTtn($id)
    {
        $model = $this->findModel($id);

        if($this->printType == 'html'){
            Yii::$app->layout = 'print-html';
            return $this->render('_print-registry-ttn-html',['model'=>$model]);
        }
        return $this->render('_print-registry-ttn-pdf', ['model' => $model]);
    }
}
