<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-stockDepartment',
    'name' => 'NOMADEX',
    'basePath' => dirname(__DIR__),
//    'defaultRoute'=>'',
//    'language' => 'ru',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'stockDepartment\controllers',
    'modules' => [
		'alix' => [
			'class' =>'stockDepartment\modules\alix\alix',
		],
	    'ecommerce' => [
            'class' => 'app\modules\ecommerce\Module',
        ],
        'placementUnit' => [
            'class' => 'app\modules\placementUnit\placementUnit',
        ],
        'sheetShipment' => [
            'class' => 'app\modules\sheetShipment\sheetShipment',
        ],
        'wms' => [
            'class' => 'app\modules\wms\wms',
        ],
        'tms' => [
            'class' => 'app\modules\tms\tms',
        ],
        'operatorDella' => [
            'class' => 'app\modules\operatorDella\operatorDella',
        ],
        'route' => [
            'class' => 'app\modules\route\route',
        ],
        'bookkeeper' => [
            'class' => 'app\modules\bookkeeper\bookkeeper',
        ],
        'freeenter' => [
            'class' => 'app\modules\freeenter\freeenter',
        ],
        'warehouseDistribution' => [
            'class' => 'app\modules\warehouseDistribution\warehouseDistribution',
        ],
        'crossDock' => [
            'class' => 'stockDepartment\modules\crossDock\crossDock',
        ],
        'other' => [
            'class' => 'stockDepartment\modules\other\Other',
        ],
        'returnOrder' => [
            'class' => 'app\modules\returnOrder\returnOrder',
        ],
        'agentBilling' => [
            'class' => 'app\modules\agentBilling\agentBilling',
        ],
        'report' => [
            'class' => 'stockDepartment\modules\report\report',
        ],
        'kpiSettings' => [
            'class' => 'stockDepartment\modules\kpiSettings\kpiSettings',
        ],
        'employee' => [
            'class' => 'app\modules\employee\employee',
        ],
        'billing' => [
            'class' => 'app\modules\billing\billing',
        ],
        'audit' => [
            'class' => 'app\modules\audit\audit',
        ],
        'outbound' => [
            'class' => 'app\modules\outbound\outbound',
        ],
        'city' => [
            'class' => 'app\modules\city\city',
        ],
        'transportLogistics' => [
            'class' => 'app\modules\transportLogistics\transportLogistics',
//            'on eventPrintTtn' => function ($event) {
//                \yii\helpers\VarDumper::dump($event);
//                die('on eventPrintTtn');
//                 Yii::info("transportLogistics : eventPrintTtn");
//            },
        ],
        'stock' => [
            'class' => 'app\modules\stock\stock',
        ],
		'inbound' => [
			'class' => 'app\modules\inbound\inbound',
		],
		'warehouse' => [
			'class' => 'app\modules\warehouse\warehouse',
		],
        'order' => [
            'class' => 'app\modules\order\order',
        ],
        'product' => [
            'class' => 'app\modules\product\product',
        ],
        'codebook' => [
            'class' => 'app\modules\codebook\codebook',
        ],
        'client' => [
            'class' => 'app\modules\client\client',
//            'enableUnconfirmedLogin' => true,
//            'confirmWithin' => 21600,
//            'cost' => 12,
//            'admins' => ['test01', 'Ferze']
//            'components' => [
//                'manager' => [
//                    // Active record classes
//                    'userClass'    => 'stockDepartment\modules\client\models\User',
//                    'profileClass' => 'stockDepartment\modules\client\models\Profile',
//                    'userSearchClass' => 'stockDepartment\modules\client\models\UserSearch',
//                ]
//            ]
        ],
        'store' => [
            'class' => 'app\modules\store\store',
        ],
        'leads' => [
            'class' => 'app\modules\leads\leads',
        ],
        'user' => [
            'class' => 'dektrium\user\Module',
//            'allowUnconfirmedLogin' => true,
            'enableUnconfirmedLogin' => true,
            'confirmWithin' => 21600,
            'cost' => 12,
            'admins' => ['test01', 'Ferze'],
//            'components' => [
//                'manager' => [
//                    'userClass' => 'common\modules\user\models\User',
//                    'userSearchClass' => 'stockDepartment\modules\user\models\UserSearch',
//                ],
//            ],
            'modelMap'=>[
                'user'=>'common\modules\user\models\User',
                'userSearch'=>'stockDepartment\modules\user\models\UserSearch',
            ],
            'controllerMap' => [
//                'admin' => 'app\modules\user\controllers\AdminController'
                'security' => 'app\modules\user\controllers\SecurityController'
            ],
        ],
//        'datecontrol' =>  [
//            'class' => '\kartik\datecontrol\Module',
//        ],
		'mobile' => [
			'class' => 'stockDepartment\modules\mobile\Mobile',
		],
        'kaspi' => [
            'class' => 'stockDepartment\modules\kaspi\kaspi',
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
//            'showScriptName' => false,
//            'enableStrictParsing' => true,
            'rules'=> [
                //'order/order-process/set-product-status-printed-label/box_barcode/<box_barcode:[\w\-]+>/store_id/<store_id:\d+>'=>'order/order-process/set-product-status-printed-label',
                //'order/order-process/set-product-status-printed-box-label/store_id/<store_id:\d+>'=>'order/order-process/set-product-status-printed-box-label',
                //'user'=>'user/admin/index',
								[
//					'class' => 'yii\rest\UrlRule',
					'pattern' => "alix/api/v1/stock",
					"route"=>"alix/api/v1/stock/index",

//					'pattern' => "wms/erenRetail/api/v1/stock",
//					"route"=>"wms/erenRetail/api/v1/stock/index",
					'verb'=>'POST'
				],
				[
//					'class' => 'yii\rest\UrlRule',
					'pattern' => "alix/api/v1/inbound/orders",
					"route"=>"alix/api/v1/inbound/orders",
					'verb'=>'POST'
				],
				[
//					'class' => 'yii\rest\UrlRule',
					'pattern' => "alix/ecommerce/api/v1/outbound/orders",
					"route"=>"alix/ecommerce/api/v1/outbound/orders",
					'verb'=>'POST'
				],
				[
//					'class' => 'yii\rest\UrlRule',
					'pattern' => "alix/api/v1/outbound/orders",
					"route"=>"alix/api/v1/outbound/orders",
					'verb'=>'POST'
				],
				[
//					'class' => 'yii\rest\UrlRule',
					'pattern' => "alix/ecommerce/api/v1/outbound/cancel",
					"route"=>"alix/ecommerce/api/v1/outbound/cancel",
					'verb'=>'POST'
				],
				[
//					'class' => 'yii\rest\UrlRule',
					'pattern' => "alix/api/v1/outbound/reports",
					"route"=>"alix/api/v1/outbound/reports",
					'verb'=>'GET'
				],
				[
//					'class' => 'yii\rest\UrlRule',
					'pattern' => "alix/api/v1/inbound/returns",
					"route"=>"alix/api/v1/inbound/returns",
					'verb'=>'POST'
				],
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
			'parsers' => [
				'application/json' => 'yii\web\JsonParser',
			],
        ],
//        'user' => [
//            'identityClass' => 'common\models\User',
//            'enableAutoLogin' => true,
//        ],
//        'log' => [
//            'traceLevel' => YII_DEBUG ? 3 : 0,
//            'targets' => [
//                [
//                    'class' => 'yii\log\FileTarget',
//                    'levels' => ['error', 'warning'],
//                ],
//            ],
//            [
//                'class' => 'yii\log\EmailTarget',
//                'levels' => ['error'],
//                'categories' => ['yii\db\*'],
//                'message' => [
//                    'from' => ['log@example.com'],
//                    'to' => ['admin@example.com', 'developer@example.com'],
//                    'subject' => 'Database errors at example.com',
//                ],
//            ],
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
                        'subject' => 'Errors on stock nmdx.kz',
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
