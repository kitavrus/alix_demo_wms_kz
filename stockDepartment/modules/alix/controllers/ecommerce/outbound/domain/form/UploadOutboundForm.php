<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\form;

use yii\base\Model;
use Yii;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class UploadOutboundForm extends Model {

	public $file;

	/**
	 *
	 * */
	public function attributeLabels()
	{
		return [
			'file' => Yii::t('return/forms', 'File'),
		];
	}

	/**
	 *
	 * */
	public function rules()
	{
		return [
			[['file'], 'file', 'checkExtensionByMimeType' => false, 'extensions' => 'csv,xls,xlsx'],
		];
	}
}