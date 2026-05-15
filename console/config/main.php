<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'cost' => 12,
        ],
        'kaspi' => [
            'class'         => 'stockDepartment\modules\kaspi\kaspi',
            'apiToken'      => '+vWV5nZLFOVPEisce0YR9doMiBlv0NKfclVukFWP1SM=',
            'useMock'       => false,
            'httpLog'       => true,
            'kaspiClientId' => \common\modules\client\models\Client::CLIENT_ALIXAVIEN,
        ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'except' => ['alix.1c', 'alix.1c.*', 'kaspi', 'kaspi.*'],
                ],
                [
                    'class' => 'common\log\IntegrationFileTarget',
                    'logFile' => '@runtime/logs/alix/1c.log',
                    'categories' => ['alix.1c', 'alix.1c.*'],
                    'levels' => ['error', 'warning', 'info', 'trace'],
                    'logVars' => [],
                ],
                [
                    'class' => 'common\log\IntegrationFileTarget',
                    'logFile' => '@runtime/logs/kaspi/kaspi.log',
                    'categories' => ['kaspi', 'kaspi.*'],
                    'levels' => ['error', 'warning', 'info', 'trace'],
                    'logVars' => [],
                ],
            ],
        ],
    ],
    'params' => $params,
];
