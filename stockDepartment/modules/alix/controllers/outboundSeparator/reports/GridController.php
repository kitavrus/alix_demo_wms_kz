<?php

namespace stockDepartment\modules\alix\controllers\outboundSeparator\reports;

use stockDepartment\modules\intermode\controllers\outboundSeparator\entities\OutboundSeparatorItemsSearch;
use Yii;
use stockDepartment\modules\intermode\controllers\outboundSeparator\entities\OutboundSeparator;
use stockDepartment\modules\intermode\controllers\outboundSeparator\entities\OutboundSeparatorSerach;
use stockDepartment\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GridController implements the CRUD actions for OutboundSeparator model.
 */
class GridController extends Controller
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
     * Lists all OutboundSeparator models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OutboundSeparatorSerach();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OutboundSeparator model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
	public function actionView($id)
	{
		$model = $this->findModel($id);
		$itemSearch = new OutboundSeparatorItemsSearch();
		$ItemsProvider = $itemSearch->search(Yii::$app->request->queryParams);
		$ItemsProvider->query->andWhere(['outbound_separator_id' => $model->id]);

		return $this->render('view', [
			'model' => $model,
			'ItemsProvider' => $ItemsProvider,
			'searchModel' => $itemSearch,
		]);
	}

    /**
     * Creates a new OutboundSeparator model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OutboundSeparator();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OutboundSeparator model.
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
     * Deletes an existing OutboundSeparator model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the OutboundSeparator model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OutboundSeparator the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OutboundSeparator::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
