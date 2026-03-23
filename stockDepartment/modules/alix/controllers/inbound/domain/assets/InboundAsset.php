<?php

namespace stockDepartment\modules\intermode\controllers\inbound\domain\assets;

use yii\web\AssetBundle;

class InboundAsset extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';

	public $js = [
		'js/erenRetail/v2/scan-inbound-form.js',

	];

	public $jsOptions = [
		'position' => \yii\web\View::POS_BEGIN
	];
}
