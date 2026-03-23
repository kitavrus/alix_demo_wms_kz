<?php
namespace stockDepartment\modules\wms\assets\hyundaiAuto;

use yii\web\AssetBundle;

class ScanInboundFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/hyundaiAuto/scan-inbound-form.js',

    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}