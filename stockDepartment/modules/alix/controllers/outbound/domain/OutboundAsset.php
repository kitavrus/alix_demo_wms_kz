<?php
namespace stockDepartment\modules\intermode\controllers\outbound\domain;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class OutboundAsset extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';

	public $js = [
//		'js/erenRetail/outbound-process.js',
		'js/intermode/outbound/scanning-form.js',
	];

	public $depends = [
		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapAsset',
	];

	public $jsOptions = [
		'position' => \yii\web\View::POS_HEAD
	];
}

/*{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
//        'css/site.css',
    ];
    public $js = [
        'js/outbound-process.js',
//        'js/transport-logistics.js',
    ];
    public $depends = [
//        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_READY
    ];
}*/
