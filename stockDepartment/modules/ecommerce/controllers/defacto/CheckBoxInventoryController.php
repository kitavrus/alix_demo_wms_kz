<?php

namespace app\modules\ecommerce\controllers\defacto;

use common\ecommerce\constants\CheckBoxStatus;
use common\ecommerce\constants\CheckBoxType;
use common\ecommerce\defacto\checkBox\service\CheckBoxService;
use common\ecommerce\entities\EcommerceCheckBox;
use common\ecommerce\entities\EcommerceCheckBoxStock;
use common\ecommerce\entities\EcommerceStock;
use Yii;
use common\ecommerce\entities\EcommerceCheckBoxInventory;
use common\ecommerce\entities\EcommerceCheckBoxInventorySearch;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CheckBoxInventoryController implements the CRUD actions for EcommerceCheckBoxInventory model.
 */
class CheckBoxInventoryController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all EcommerceCheckBoxInventory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EcommerceCheckBoxInventorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EcommerceCheckBoxInventory model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EcommerceCheckBoxInventory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EcommerceCheckBoxInventory();
        $model->inventory_key = date('d-m-Y');
        $model->description = 'Ежедневный';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->status = CheckBoxStatus::NEW_;
            $model->save(false);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EcommerceCheckBoxInventory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing EcommerceCheckBoxInventory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Deletes an existing EcommerceCheckBoxInventory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionComplete($id)
    {
        $inventoryInfo = $this->findModel($id);
        if (!CheckBoxStatus::isDone($inventoryInfo->status)) {

            $checkBoxService = new CheckBoxService();
            $checkBoxService->complete($inventoryInfo->id);

//            $inventoryInfo->complete_date = time();
//            $inventoryInfo->status = CheckBoxStatus::DONE;
//            $inventoryInfo->save(false);
        } else {
            Yii::$app->session->setFlash('danger', " <b>[ ".$inventoryInfo->inventory_key." ]</b> Эта инвент-ция уже закрыта");
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the EcommerceCheckBoxInventory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EcommerceCheckBoxInventory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EcommerceCheckBoxInventory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionLoadAllBoxForFullInventory() {
        // /ecommerce/defacto/check-box-inventory/load-all-box-for-full-inventory

        $service = new CheckBoxService();
        $inventoryId = 1;
        $service->loadAllBoxForFullInventory($inventoryId,CheckBoxType::FULL);

        $inventoryInfo = $service->getInventoryInfo($inventoryId);
        Yii::$app->session->setFlash('success', " <b>[ ".$inventoryInfo->inventory_key." ]</b> Полная инвентаризация успешно создана");
        return $this->redirect(['index']);
    }

    public function actionLoadAllBoxByRowAddress() {
        // /ecommerce/defacto/check-box-inventory/load-all-box-by-row-address

        $service = new CheckBoxService();
        $inventoryId = 8;
        $placeAddress = '4-8-14-1';
        $minMaxPlaceAddress = $service->getMinMaxSecondaryAddress($placeAddress);
        $service->loadAllBoxByRowAddress($inventoryId,$minMaxPlaceAddress,CheckBoxType::FULL);

        $inventoryInfo = $service->getInventoryInfo($inventoryId);
        Yii::$app->session->setFlash('success', " <b>[ ".$inventoryInfo->inventory_key." ]</b>" . "Для ряда : "." <b>[ ".$placeAddress." ]</b> инвентаризация успешно создана");
        return $this->redirect(['index']);
    }

    public function actionDeleteBox() {
        // /ecommerce/defacto/check-box-inventory/delete-box

        $service = new CheckBoxService();
        $inventoryId = 8;
        $boxAddress = '100000008141';
        $service->deleteBox($inventoryId,$boxAddress);

        $inventoryInfo = $service->getInventoryInfo($inventoryId);
        Yii::$app->session->setFlash('success', " <b>[ ".$inventoryInfo->inventory_key." ]</b>" . "Короб  : "." <b>[ ".$boxAddress." ]</b> успешно удален");
        return $this->redirect(['index']);
    }

    public function actionDeleteInventory() {
        // /ecommerce/defacto/check-box-inventory/delete-inventory

        $service = new CheckBoxService();
        $inventoryId = 8;
        $inventoryInfo = $service->getInventoryInfo($inventoryId);
        $service->deleteInventory($inventoryId);


        Yii::$app->session->setFlash('success', " <b>[ ".$inventoryInfo->inventory_key." ]</b>" . "Инвентаризация  : успешно удалена");
        return $this->redirect(['index']);
    }

    public function actionResetRow() {
        // /ecommerce/defacto/check-box-inventory/reset-row

        $service = new CheckBoxService();
        $inventoryId = 8;
        $placeAddress = '4-8-14-1';
        $service->resetRow($inventoryId,$placeAddress);
        $inventoryInfo = $service->getInventoryInfo($inventoryId);

        Yii::$app->session->setFlash('success', " <b>[ ".$inventoryInfo->inventory_key." ]</b>" . "Для ряда : "." <b>[ ".$placeAddress." ]</b> инвентаризации успешно сброшен");
        return $this->redirect(['index']);
    }

    public function actionTest() {
        // /ecommerce/defacto/check-box-inventory/test
        die("/ecommerce/defacto/check-box-inventory/test");

        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";

        return 'OK';
    }
}
