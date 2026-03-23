<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-clientDepartment',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'clientDepartment\controllers',
//    'language' => 'ru',

    'bootstrap' => ['log'],
    'modules' => [
        'warehouseDistribution' => [
            'class' => 'app\modules\warehouseDistribution\warehouseDistribution',
        ],
        'report'=>[
            'class' => 'app\modules\report\report'
        ],
        'returnOrder'=>[
            'class' => 'app\modules\returnOrder\returnOrder'
        ],
        'city' => [
            'class' => 'app\modules\city\city',
        ],
        'store' => [
            'class' => 'app\modules\store\store',
        ],
        'transportLogistics' => [
            'class' => 'app\modules\transportLogistics\transportLogistics',
        ],
        'client' => [
            'class' => 'app\modules\client\client',
        ],
        'user' => [
            'class' => 'dektrium\user\Module',
//            'allowUnconfirmedLogin' => true,
            'enableUnconfirmedLogin' => true,
            'confirmWithin' => 21600,
            'cost' => 12,
            'admins' => ['test01'],
//            'urlPrefix' => 'client',
//            'components' => [
//                'manager' => [
//                    'class'=>'dektrium\user\Module',
//                    'userClass' => 'common\modules\user\models\User',
//                    'userSearchClass' => 'clientDepartment\modules\user\models\UserSearch', //SecurityController
//                ],
//            ],
            'modelMap'=>[
                'user'=>'common\modules\user\models\User',
                'userSearch'=>'clientDepartment\modules\user\models\UserSearch',
            ],
            'controllerMap' => [
                'admin' => 'app\modules\user\controllers\AdminController',
                'security' => 'app\modules\user\controllers\SecurityController'
            ],
        ],
    ],
    'components' => [
//        'formatter' => [
//            'class' => 'yii\i18n\Formatter',
//            'currencyCode'=> 'KZT'
//        ],
        'view' => [
            'theme' => [
                'pathMap' => [
//                    '@dektrium/user/views' => '@app/views/user'
                       '@dektrium/user/views' => '@app/modules/user/views'
                ],
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'hwMetJ2K0PiZf1cFzcRjMzNLzSAk9VhX',
        ],
//        'user' => [
//            'identityClass' => 'common\models\User',
//            'enableAutoLogin' => true,
//        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\EmailTarget',
                    'levels' => ['error', 'warning'],
                    'categories' => ['yii\db\*'],
                    'message' => [
                        'from' => ['log@nmdx.kz'],
                        'to' => ['kitavrus@ya.ru'],
                        'subject' => 'Errors on client nmdx.kz',
                    ],
                ],
            ]
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'on beforeRequest' => function ($event) {
        if (!Yii::$app->user->isGuest) {
//            if ($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {
//            $this->finder = \Yii::$container->get(Finder::className());


//            \yii\helpers\VarDumper::dump(\Yii::$container->get(dektrium\user\Finder::className())->findUserById(Yii::$app->user->id),10,true);
//            \yii\helpers\VarDumper::dump(Yii::$app->getModule('user')->user,10,true);
//            die;
//            if ($userModel = Yii::$app->getModule('user')->get('finder')->findUserById(Yii::$app->user->id)) {
            if ($userModel = \Yii::$container->get(dektrium\user\Finder::className())->findUserById(Yii::$app->user->id)) {
                if( $client = \common\modules\client\models\ClientEmployees::findOne(['user_id'=>$userModel->id]) ) {
                    switch ($client->manager_type) {
                        case \common\modules\client\models\ClientEmployees::TYPE_OBSERVER:
                        case \common\modules\client\models\ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
                            Yii::$app->language = 'tr';
                            break;
                    }
					
                    switch ($client->id) {
                        case '350':
                            Yii::$app->language = 'tr';
                            break;
                    }
                }
            }
        }
    },
    'params' => $params,
];
