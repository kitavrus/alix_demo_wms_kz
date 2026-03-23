<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace app\modules\warehouseDistribution\models;

use common\components\BarcodeManager;
use common\modules\returnOrder\models\ReturnOrder;
use common\modules\returnOrder\models\ReturnOrderItems;
use yii\base\Model;
use Yii;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class ErenRetailOutboundForm extends Model {

	public $from_point;
	public $to_point;
	public $outbound_order_number;
	public $file;
	public $description;

	const FILE_COLUMN_QTY = 10;


	/*
	 *
	 *
	 * */
	public function attributeLabels()
	{
		return [
			'from_point' => Yii::t('forms', 'From Point'),
			'to_point' => Yii::t('forms', 'To Point'),
			'outbound_order_number' => Yii::t('return/forms', 'Номер накладной'),
			'file' => Yii::t('return/forms', 'File'),
			'description' => Yii::t('forms', 'Комментарий'),
		];
	}

	/*
	 *
	 *
	 * */
	public function rules()
	{
		return [
			[['outbound_order_number'], 'trim'],
			[['from_point', 'to_point','outbound_order_number'], 'required'],
			[['from_point', 'to_point'], 'integer'],
			[['file'], 'file', 'checkExtensionByMimeType' => false, 'extensions' => 'csv'],
			[['description'], 'string'],
		];
	}
}