<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=wms20',
            'username' => 'root',
            'password' => 'ApJffsEcmD',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
//            'initSQLs'=>array("set time_zone='+00:00';"),
        ],
        'dbAudit' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=wms20_audit',
            'username' => 'root',
            'password' => 'ApJffsEcmD',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
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
/*

// включение кэширование схемы БД
'enableSchemaCache' => true,
// длительность кэша
'schemaCacheDuration' => 3600,
// компонент кэша
'schemaCache' => 'cache',

php yii cache/flush-schema

*/