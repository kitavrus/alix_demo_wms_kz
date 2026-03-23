<?php

namespace app\modules\intermode\controllers\cronManager;

use Yii;
use stockDepartment\modules\intermode\controllers\cronManager\domains\cron_manager\CronManagerSearch;
use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * ReportController implements the CRUD actions for CronManager model.
 */
class ReportController extends Controller
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
     * Lists all CronManager models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CronManagerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}