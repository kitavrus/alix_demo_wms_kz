<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-personalDepartment',
    'name' => 'NMDX',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\controllers',
    'language' => 'ru',
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'enablePasswordRecovery' => true,
            'enableUnconfirmedLogin' => true,
            'confirmWithin' => 21600,
            'cost' => 12,
            'admins' => ['test01'],
            'mailer' => [
                'sender'                => 'no-reply@nmdx.kz',
                'welcomeSubject'        => Yii::t('client/mail','NMDX: Account was successfully created'),
                'confirmationSubject'   => Yii::t('client/mail','NMDX: Please confirm your registration'),
                'reconfirmationSubject' => Yii::t('client/mail','NMDX: Please confirm your registration'),
                'recoverySubject'       => Yii::t('client/mail','NMDX: password recovery'),
                'viewPath'              =>'@app/modules/user/views/mail',
            ],
            'modelMap'=>[
                'user'=>'common\modules\user\models\User',
                'userSearch'=>'app\modules\user\models\UserSearch',
            ],
            'controllerMap' => [
                'security' => 'app\modules\user\controllers\SecurityController',
                'registration' => 'app\modules\user\controllers\RegistrationController',
                'recovery' => 'app\modules\user\controllers\RecoveryController',
            ],
        ],
        'address' => [
            'class' => 'app\modules\address\address',
        ],
        'order' => [
            'class' => 'app\modules\order\order',
        ],
        'client' => [
            'class' => 'app\modules\client\client',
        ],
    ],
    'components' => [
        
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
                        'subject' => 'Errors on personal nmdx.kz',
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
