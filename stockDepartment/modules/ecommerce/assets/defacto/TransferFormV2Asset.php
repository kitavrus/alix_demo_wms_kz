<?php
namespace app\modules\ecommerce\assets\defacto;

use yii\web\AssetBundle;

class TransferFormV2Asset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/e-commerce/defacto/transfer-form-v2.js',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}