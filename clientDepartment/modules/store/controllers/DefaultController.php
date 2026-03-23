<?php

namespace app\modules\store\controllers;

use common\modules\client\models\ClientEmployees;
use Yii;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use clientDepartment\components\Controller;
use common\modules\store\models\Store;
use clientDepartment\modules\store\models\StoreSearch;
use clientDepartment\modules\client\components\ClientManager;

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
        if(!ClientManager::canIndexStore(null) ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $searchModel = new StoreSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render($this->getViewByType('index'), [
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
        $model = $this->findModel($id);

        if(!ClientManager::canViewStore($model) ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        return $this->render($this->getViewByType('view'), [
            'model' => $model,
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

        if(!ClientManager::canCreateStore($model) ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        if ($model->load(Yii::$app->request->post()) )  {
            $model->client_id = ClientManager::getClientEmployeeByAuthUser()->client_id;
            if( $model->save()) {
                $model->setInternalCode();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $model->status = Store::STATUS_ACTIVE;
        return $this->render('create', [
            'model' => $model,
        ]);
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

        if(!ClientManager::canUpdateStore($model) ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render($this->getViewByType('update'), [
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
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

//        VarDumper::dump($model,10,true);

        if(!ClientManager::canDeleteStore($model) ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        if($model) {
            $model->deleted = 1;
            $model->save(false);
        }

//        $rt = ['/client/employees/index'];
//
//        switch(Yii::$app->request->post('rt')) {
//            case 'c':
//                $rt = ['/client/default/view'];
//                break;
//            case 'store':
//                $rt = ['/store/default/view', 'id' => $model->store_id];
//                break;
//            default:
//                $rt = ['/client/employees/index'];
//                break;
//        }

        return $this->redirect(['index']);
    }

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
     * Redirect to view
     *
     * */
    public function actionViewRedirect()
    {
        $client = ClientManager::getClientEmployeeByAuthUser();
        $this->redirect(['/store/default/view','id'=>$client->store_id]);
    }

    /*
  * Get view type
  * @param string $view Action name
  * @return string view name
  * */
    protected function getViewByType_TO_DELETE($view)
    {
        if(!Yii::$app->user->isGuest) {

//            if($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if ($client = ClientManager::getClientEmployeeByAuthUser()) {
//                    VarDumper::dump($client,10,true);
                    switch ($client->manager_type) {
                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                            $view .= '';
                            break;
                        case ClientEmployees::TYPE_OBSERVER:
                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
                            $view = 'observer/'.$view;
                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $view = 'manager/'.$view;
                            break;

                        default:
//                            $view .= 'default/'.$view;
                            $view = 'empty-default';
                            break;
                    }
//                }
            } else {
                $view = 'empty-default';
            }
        } else {
            $view = 'empty-default';
        }
        return $view;
    }
}
