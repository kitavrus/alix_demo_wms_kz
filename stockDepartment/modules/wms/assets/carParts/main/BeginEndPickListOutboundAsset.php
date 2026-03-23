<?php
namespace stockDepartment\modules\wms\assets\carParts\main;

use yii\web\AssetBundle;

class BeginEndPickListOutboundAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/carParts/main/begin-end-pick-list-form-outbound.js',

    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}