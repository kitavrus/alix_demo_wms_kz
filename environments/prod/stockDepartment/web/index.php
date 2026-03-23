<?php
//date_default_timezone_set('Asia/Almaty');
set_time_limit(0);
ini_set('memory_limit', '-1');
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('default_socket_timeout', 600);
ini_set("soap.wsdl_cache_enabled", "0");

defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/aliases.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../common/config/main-local.php'),
    require(__DIR__ . '/../config/main.php'),
    require(__DIR__ . '/../config/main-local.php')
);

$application = new yii\web\Application($config);
$application->run();
