<?php

namespace app\modules\product\controllers;

use common\modules\product\models\ProductBarcodes;
use stockDepartment\modules\outbound\models\OutboundOrderItemSearch;
use stockDepartment\modules\product\models\ProductBarcodesSearch;
use Yii;
use yii\db\Expression;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use stockDepartment\components\Controller;
use common\modules\product\models\Product;
use stockDepartment\modules\product\models\ProductSearch;

/**
 * DefaultController implements the CRUD actions for Product model.
 */
class DefaultController extends Controller
{
//    public function behaviors()
//    {
//        $b = [
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['post'],
//                ],
//            ],
//        ];
//        return array_merge(parent::behaviors(),$b);
//    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		$model = $this->findModel($id);
		$itemSearch = new ProductBarcodesSearch();
		$itemsProvider = $itemSearch->search(Yii::$app->request->queryParams);
		$itemsProvider->query->andWhere(['product_id' => $model->id]);

        return $this->render('view', [
            'model' =>$model,
            'itemSearch' =>$itemSearch,
            'itemsProvider' =>$itemsProvider,
        ]);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Product();
		$model->client_id = 103;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$model->client_id = 103;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


	/**
	 * Displays a single Product model.
	 * @param integer $id
	 * @param integer $barcodeId
	 * @return mixed
	 */
	public function actionUpdateBarcode($id)
	{
		$model = $this->findModelBarcode($id);
		$model->client_id = 103;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->product_id]);
		} else {
			return $this->render('barcodes/update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Creates a new Product model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreateBarcode($product_id)
	{
		$model = new ProductBarcodes();
		$model->client_id = 103;
		$model->product_id = $product_id;
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->product_id]);
		} else {
			return $this->render('barcodes/create', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Finds the Product model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Product the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModelBarcode($id)
	{
		if (($model = ProductBarcodes::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
