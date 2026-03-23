<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-bossDepartment',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'bossDepartment\controllers',
//    'language' => 'ru',
    'bootstrap' => ['log'],
    'modules' => [
          'report'=>[
              'class' => 'bossDepartment\modules\report\report'
          ],
//        'city' => [
//            'class' => 'app\modules\city\city',
//        ],
//        'store' => [
//            'class' => 'app\modules\store\store',
//        ],
//        'transportLogistics' => [
//            'class' => 'app\modules\transportLogistics\transportLogistics',
//        ],
//        'client' => [
//            'class' => 'app\modules\client\client',
//        ],

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
//                    'userClass' => 'bossDepartment\modules\user\models\User',
//                    'userSearchClass' => 'bossDepartment\modules\user\models\UserSearch', //SecurityController
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
                       '@dektrium/user/views' => '@app/modules/user/views',
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
                        'subject' => 'Errors on boss nmdx.kz',
                    ],
                ],
            ]
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,
];
