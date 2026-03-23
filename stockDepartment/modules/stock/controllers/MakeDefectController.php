<?php

namespace app\modules\stock\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use common\modules\stock\models\Stock;
use app\modules\stock\models\StockSearch;
use stockDepartment\components\Controller;

class MakeDefectController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new StockSearch();
        $params = Yii::$app->request->queryParams;
        $conditionTypeArray = $searchModel->getConditionTypeArray();
        $statusArray = $searchModel->getStatusArray();


        if (isset($params['StockSearch'])) {
            $dataProvider = $searchModel->search($params);
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => $searchModel->find()->where('1=0'),
            ]);
        }
        return $this->render('make-defect', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'conditionTypeArray' => $conditionTypeArray,
            'statusArray' => $statusArray
        ]);
    }

    /**
     * Сделать браком
     */
    public function actionMakeDefect()
    {
        $params = Yii::$app->request->post('StockSearch');
        if (!$params) {
            return $this->redirect(['index']);
        }

        $updated = Stock::updateAll(
            ['condition_type' => Stock::CONDITION_TYPE_FULL_DAMAGED],
            ['primary_address' => $params['primary_address']]
        );

        Yii::$app->session->setFlash('success',  "Короб помечено как брак: {$updated}");

        return $this->redirect(['index', 'StockSearch' => $params]);
    }

    /**
     * Сделать не браком
     */
    public function actionMakeNotDefect()
    {
        $params = Yii::$app->request->post('StockSearch');
        if (!$params) {
            return $this->redirect(['index']);
        }

        $updated = Stock::updateAll(
            ['condition_type' => Stock::CONDITION_TYPE_UNDAMAGED],
            ['primary_address' => $params['primary_address']]
        );

        Yii::$app->session->setFlash('success', "Короб помечен как не брак: {$updated}");

        return $this->redirect(['index', 'StockSearch' => $params]);
    }
}