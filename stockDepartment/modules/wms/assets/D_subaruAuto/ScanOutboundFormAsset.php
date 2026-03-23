<?php
namespace stockDepartment\modules\wms\assets\subaruAuto;

use yii\web\AssetBundle;

class ScanOutboundFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/subaruAuto/scan-outbound-form.js',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}