<?php
namespace common\b2b\domains\checkBox\assets\defacto;

use yii\web\AssetBundle;

class CheckBoxFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/b2b/defacto/check-box-form.js',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}