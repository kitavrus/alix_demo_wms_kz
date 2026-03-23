<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontendDepartment',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontendDepartment\controllers',
    'language' => 'ru',
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            //'allowUnconfirmedLogin' => true,
            'enableUnconfirmedLogin' => true,
            'confirmWithin' => 21600,
            'cost' => 12,
            'admins' => ['test01'],
            'modelMap'=>[
                'user'=>'common\modules\user\models\User',
                'userSearch'=>'frontendDepartment\modules\user\models\UserSearch',
            ],
            'controllerMap' => [
                'security' => 'app\modules\user\controllers\SecurityController',
                'registration' => 'app\modules\user\controllers\RegistrationController',
            ],
        ],
        'tariff' => [
            'class' => 'frontendDepartment\modules\tariff\tariff',
        ],
        'order' => [
            'class' => 'frontendDepartment\modules\order\order',
        ],

    ],
    'components' => [
//        'formatter' => [
//            'class' => 'yii\i18n\Formatter',
//            'currencyCode'=> 'KZT'
//         ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
//            'urlFormat' => 'path',
//            'enableStrictParsing' => true,
            'rules'=> [

            ]
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
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
                        'subject' => 'Errors on frontend nmdx.kz',
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
