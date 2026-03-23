<?php

namespace stockDepartment\modules\alix\controllers\outboundSeparator\scanning\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class FormAsset extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';

	public $js = [
		'js/alix/outbound_separator/scanning/form-v1.js',
	];

	public $depends = [
		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapAsset',
	];

	public $jsOptions = [
		'position' => \yii\web\View::POS_HEAD
	];
}