<?php
namespace app\modules\intermode\assets;

use yii\web\AssetBundle;

class ScanInboundFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/e-commerce/intermode/scan-inbound-form.js',

    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}