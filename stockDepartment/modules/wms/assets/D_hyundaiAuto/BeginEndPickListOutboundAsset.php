<?php
namespace stockDepartment\modules\wms\assets\hyundaiAuto;

use yii\web\AssetBundle;

class BeginEndPickListOutboundAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/hyundaiAuto/begin-end-pick-list-form-outbound.js',

    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}