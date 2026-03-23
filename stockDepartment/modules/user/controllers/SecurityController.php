<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\modules\user\controllers;

use bossDepartment\modules\user\models\User;
use dektrium\user\models\LoginForm;
use yii\helpers\Url;
//use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\authclient\ClientInterface;
use dektrium\user\controllers\SecurityController as BaseSecurityController;
use common\modules\client\models\ClientEmployees;
use yii\helpers\VarDumper;

/**
 * Controller that manages user authentication process.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class SecurityController extends BaseSecurityController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login', 'auth'],
                        'roles' => ['?']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['logout'],
                        'roles' => ['@']
                    ],
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post']
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {

        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'authenticate'],
            ]
        ];
    }

    /**
     * Displays the login page.
     *
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
//        $model = $this->module->manager->createLoginForm();
        $model = \Yii::createObject(LoginForm::className());

        if ($model->load(\Yii::$app->getRequest()->post())) {

//            if ($client = ClientEmployees::findOne(['user_id' => \Yii::$app->user->id])){
//                if($client->id == 71){
//                    return $this->redirect('/returnOrder/default/index');
//                }
//            }
            // Find login in user table
            if(!\common\modules\user\models\User::find()->where([
                'username'=>$model->login,
                'user_type'=>\common\modules\user\models\User::USER_TYPE_STOCK_WORKER,
            ])->exists() && !in_array($model->login,['Mjalilov','Atoxanov'])) {

                $model->addError('login','У вас нет доступа к этому разделу');

                return $this->render('login', [
                    'model' => $model
                ]);
            }
//            VarDumper::dump($model,10,true);
//            die('-LOGIN-');


            if($model->login()) {
                return $this->redirect('/');
            }

            // TODO Сделать проверку на тип того кто логинится и отправлять его на другие страницы
//            return $this->goBack();
        }

        return $this->render('login', [
            'model' => $model
        ]);
    }

    /**
     * Logs the user out and then redirects to the homepage.
     *
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        \Yii::$app->getUser()->logout();

        return $this->goHome();
    }

    /**
     * Logs the user in if this social account has been already used. Otherwise shows registration form.
     *
     * @param  ClientInterface $client
     * @return \yii\web\Response
     */
    public function authenticate(ClientInterface $client)
    {
        $attributes = $client->getUserAttributes();
        $provider   = $client->getId();
        $clientId   = $attributes['id'];

        if (null === ($account = $this->module->manager->findAccount($provider, $clientId))) {
            $account = $this->module->manager->createAccount([
                'provider'   => $provider,
                'client_id'  => $clientId,
                'data'       => json_encode($attributes)
            ]);
            $account->save(false);
        }

        if (null === ($user = $account->user)) {
            $this->action->successUrl = Url::to(['/user/registration/connect', 'account_id' => $account->id]);
        } else {
            \Yii::$app->user->login($user, $this->module->rememberFor);
        }
    }
}
