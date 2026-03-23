<?php
use kartik\datecontrol\Module;
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
//    'timeZone'=>'GMT',
//    'timeZone'=>'Europe/Kiev',
//    'language' => 'ru-RU',
    'language' => 'ru',
    'timeZone'=>'UTC',
    'components' => [
        'tcpdf' => [
            'class' => 'cinghie\tcpdf\TCPDF',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
            'transport' => [
              //'class' => 'Swift_SmtpTransport',
//              'host' => 'smtp.yandex.ru',
//              'username' => 'kitavrus',
//              'password' => '123!@#ghbdtn-flvby',
//              'port' => '465',
//              'encryption' => 'ssl',

//                'host'=>'smtp.yandex.ru',
//                'username'=>'support@vairis.com',
//                'password'=>'GhBDtN!$$$^&seggjhn',
//                'port'=>'465',
//                'encryption'=>'ssl',
            ],
            'messageConfig' => [
                'from' => ['admin@website.com' => 'Admin'], // this is needed for sending emails
                'charset' => 'UTF-8',
            ]
        ],
//        'mailer' => [
//            'class' => 'yii\swiftmailer\Mailer',
//            'viewPath' => '@common/mail',
//            // send all mails to a file by default. You have to set
//            // 'useFileTransport' to false and configure a transport
//            // for the mailer to send real emails.
//            'useFileTransport' => true,
//        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                        'basePath' => '@common/messages',
                        'sourceLanguage' => 'en',
                    'fileMap' => [
                        'buttons' => 'buttons.php',

                    ],
                ],
            ],
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'currencyCode'=> 'KZT',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'defaultTimeZone'=>'UTC',
            'timeZone'=>'Asia/Almaty',
        ],
    ],
    'modules' => [
        'datecontrol' =>  [
            'class' => 'kartik\datecontrol\Module',

//            'class' => '\kartik\datecontrol\Module',
//            'displayFormat' => 'php:d-m-Y H:i:s',
//            'saveFormat' => 'php:Y-m-d H:i:s',
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module'
        ]
    ]
];
