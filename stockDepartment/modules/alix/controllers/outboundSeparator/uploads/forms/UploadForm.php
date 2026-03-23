<?php
namespace stockDepartment\modules\alix\controllers\outboundSeparator\uploads\forms;

use yii\base\Model;
use Yii;

class UploadForm extends Model {

	public $file;
	public $order_name;
	public $comments;

	/**
	 *
	 * */
	public function attributeLabels()
	{
		return [
			'file' => Yii::t('return/forms', 'Файл с данными'),
			'order_name' => Yii::t('return/forms', 'Название задачи'),
			'comments' => Yii::t('return/forms', 'Комментарий'),
		];
	}

	/**
	 *
	 * */
	public function rules()
	{
		return [
			[['order_name','comments'], 'trim'],
			[['order_name','comments'], 'string'],
			[['file'], 'file', 'checkExtensionByMimeType' => false, 'extensions' => 'csv,xls,xlsx'],
		];
	}
}