<?php

namespace stockDepartment\modules\other\domain\outboundBoxMap;

use yii\web\AssetBundle;

class OutboundBoxMapFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
//        '/js/erenRetail/box-to-box-form.js',
        '/js/erenRetail/other/box-to-box-form.js',

    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}