<?php
namespace clientDepartment\controllers;

use common\modules\client\models\ClientEmployees;
use Yii;
use yii\filters\AccessControl;
//use yii\web\Controller;
use clientDepartment\components\Controller;
use common\models\LoginForm;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
//    public function behaviors()
//    {
//        return [
//            'access' => [
//                'class' => AccessControl::className(),
//                'rules' => [
//                    [
//                        'actions' => ['login', 'error'],
//                        'allow' => true,
//                    ],
//                    [
//                        'actions' => ['logout', 'index'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'logout' => ['post'],
//                ],
//            ],
//        ];
//    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        if ($client = ClientEmployees::findOne(['user_id' => Yii::$app->user->id])) {
            if($client->client_id == 77) { // tupperware
                return $this->redirect('/warehouseDistribution/tupperware/tl-delivery-proposal/index');
            }
        }
        $this->redirect('/transportLogistics/tl-delivery-proposal/index');

        return $this->render('index');
    }
//
//    public function actionLogin()
//    {
//        if (!\Yii::$app->user->isGuest) {
//            return $this->goHome();
//        }
//
//        $model = new LoginForm();
//        if ($model->load(Yii::$app->request->post()) && $model->login()) {
//            return $this->goBack();
//        } else {
//            return $this->render('login', [
//                'model' => $model,
//            ]);
//        }
//    }
//
//    public function actionLogout()
//    {
//        Yii::$app->user->logout();
//
//        return $this->goHome();
//    }
}
