<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace stockDepartment\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */


class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        //'css/jquery.countdown.css',
    ];
    public $js = [
        'js/core-process.js',
        'js/main.js',
        'js/order-process.js',
        'js/accommodation-stock.js',
        // //'js/inbound-process.js',
        'js/outbound-process.js',
        'js/transport-logistics.js',
        'js/jquery.countdown.js',
        // //'js/jquery.countdown.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];
}
