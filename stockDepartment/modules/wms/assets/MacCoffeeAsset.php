<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace stockDepartment\modules\wms\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MacCoffeeAsset extends AssetBundle
{
//    public $sourcePath = '@bower/bootstrap/dist';
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/warehouse-distribution/maccoffeekz-inbound.js',

    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];

    public $depends = [
        'stockDepartment\assets\AppAsset',
    ];
}