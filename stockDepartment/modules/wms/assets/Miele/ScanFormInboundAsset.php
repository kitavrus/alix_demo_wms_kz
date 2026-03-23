<?php
namespace stockDepartment\modules\wms\assets\Miele;

use yii\web\AssetBundle;

class ScanFormInboundAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/miele/scan-form-inbound.js',

    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}