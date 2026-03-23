<?php
namespace common\b2b\domains\outboundLogitrans\assets;

use yii\web\AssetBundle;

class OutboundLogiTransFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/b2b/defacto/outbound-logi-trans-form.js',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}