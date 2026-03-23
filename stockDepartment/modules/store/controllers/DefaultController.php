<?php

namespace app\modules\store\controllers;

use common\components\MailManager;
use Yii;
use yii\helpers\VarDumper;
use yii\validators\EmailValidator;
use yii\web\NotFoundHttpException;
//use yii\filters\VerbFilter;
use stockDepartment\components\Controller;
use stockDepartment\modules\store\models\StoreSearch;
use common\modules\store\models\Store;

/**
 * DefaultController implements the CRUD actions for Store model.
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
     * Lists all Store models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StoreSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Store model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        $x = 'magaza.Kzk_ADK@defacto.com.tr';
//        $x = 'magaza.Kzk_ADK@defacto.com.tr';
//        $m = $this->findModel($id);
//        $x = $m->email;
//
//        if($stores = Store::find()->where(['client_id'=>'2','type_use'=>1])->all()) {
//            $validator = new EmailValidator();
//            foreach ($stores as $store) {
//                $x = $store->email;
//                if ($validator->validate(trim($x))) {
//                if ($validator->validate($x)) {
//                    echo "OK : " . $x."<br />";
//                    echo  $x.' '.$store->id."<br />";
//                } else {
                    //TODO Send mail to admin
//                    echo "ERROR : " . $x."<br />";
//                    echo  $x.' '.$store->id."<br />";
//                }
//            }
//        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Store model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Store();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->setInternalCode();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {

            \yii\helpers\VarDumper::dump($model->getErrors(),10,true);
            $model->status = Store::STATUS_ACTIVE;
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Store model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Store model.
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
     * Finds the Store model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Store the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Store::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
     * Send email to store if expected delivery
     *
     * */

    public function actionSendEmailToStore()
    {
        $storeIDs = Yii::$app->request->post('storeIds');
        $type = Yii::$app->request->post('type');
       $mm = new MailManager();
       if($mm->SendEmailToStoreIfExpectedDelivery($storeIDs,$type)) {
           return 'OK';
       }
        return '-NO-';
    }
}
