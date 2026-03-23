<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=wms20_demo_dev',
            'username' => 'root',
            'password' => 'ApJffsEcmD',
            'charset' => 'utf8',
            'enableSchemaCache' => false,
        ],
        'dbAudit' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=wms20_audit_demo_dev',
            'username' => 'root',
            'password' => 'ApJffsEcmD',
            'charset' => 'utf8',
            'enableSchemaCache' => false,
        ],
//        'mailer' => [
//            'class' => 'yii\swiftmailer\Mailer',
//            'viewPath' => '@common/mail',
//            // send all mails to a file by default. You have to set
//            // 'useFileTransport' to false and configure a transport
//            // for the mailer to send real emails.
//            'useFileTransport' => true,
//        ],
    ],
];
