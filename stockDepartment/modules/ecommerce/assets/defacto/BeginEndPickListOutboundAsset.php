<?php
namespace app\modules\ecommerce\assets\defacto;

use yii\web\AssetBundle;

class BeginEndPickListOutboundAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/e-commerce/defacto/begin-end-pick-list-form-outbound.js',

    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}