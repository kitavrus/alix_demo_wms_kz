<?php
namespace stockDepartment\modules\wms\assets\hyundaiTruck;

use yii\web\AssetBundle;

class ScanOutboundFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/hyundaiTruck/scan-outbound-form.js',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}