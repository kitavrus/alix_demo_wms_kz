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

class ErenRetailInboundForm extends Model {

	public $orderNumber;
	public $comment;
	public $file;
	public $order_type;
	const FILE_COLUMN_QTY = 6;

	/*
	 *
	 *
	 * */
	public function attributeLabels()
	{
		return [
			'orderNumber' => Yii::t('return/forms', 'Inbound order number'),
			'comment' => Yii::t('return/forms', 'Комментарий'),
			'file' => Yii::t('return/forms', 'File'),
			'order_type' => Yii::t('return/forms', 'Тип прихода'),
		];
	}

	/*
	 *
	 *
	 * */
	public function rules()
	{
		return [
			[['orderNumber'], 'trim'],
			[['orderNumber'], 'string'],
			[['comment'], 'string'],
			[['file'], 'file', 'checkExtensionByMimeType' => false, 'extensions' => 'csv,xls,xlsx'],
			[['order_type'], 'integer'],
			[['order_type','orderNumber'], 'required'],
		];
	}
}