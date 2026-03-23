<?php
namespace app\modules\ecommerce\assets\defacto;

use yii\web\AssetBundle;

class PartReReservedFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/e-commerce/defacto/part-re-reserved-form.js',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}