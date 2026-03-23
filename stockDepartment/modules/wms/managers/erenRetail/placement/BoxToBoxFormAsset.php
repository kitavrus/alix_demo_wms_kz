<?php

namespace stockDepartment\modules\wms\managers\erenRetail\placement;

use yii\web\AssetBundle;

class BoxToBoxFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        '/js/erenRetail/box-to-box-form.js',

    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}