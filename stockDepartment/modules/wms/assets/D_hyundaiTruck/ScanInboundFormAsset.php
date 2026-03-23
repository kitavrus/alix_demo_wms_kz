<?php
namespace stockDepartment\modules\wms\assets\hyundaiTruck;

use yii\web\AssetBundle;

class ScanInboundFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/hyundaiTruck/scan-inbound-form.js',

    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}