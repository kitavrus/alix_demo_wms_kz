<?php
namespace stockDepartment\modules\alix\assets;

use yii\web\AssetBundle;

class ScanOutboundFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        // JS лежит в defacto/, форма alix использует те же ID полей и #outboundform,
        // URL-ы ручек берутся из data-url на input'ах — скрипт универсальный.
        'js/e-commerce/defacto/scan-outbound-form.js',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
}