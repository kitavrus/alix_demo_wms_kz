<?php
namespace stockDepartment\modules\wms\assets\carParts\main;

use yii\web\AssetBundle;

class BoxToBoxFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/carParts/main/box-to-box-form.js',

    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}