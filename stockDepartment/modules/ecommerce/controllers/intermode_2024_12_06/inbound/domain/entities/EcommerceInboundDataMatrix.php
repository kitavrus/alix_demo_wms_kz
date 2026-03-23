<?php

namespace app\modules\ecommerce\controllers\intermode\inbound\domain\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_inbound_data_matrix".
 *
 * @property int $id
 * @property int $inbound_id ИД приходной накладной
 * @property int $inbound_item_id ИД строки в приходной накладной
 * @property string $product_barcode Шк товара
 * @property string $product_model Модель товара
 * @property string $data_matrix_code код дата матрицы
 * @property string $status scanned
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceInboundDataMatrix extends \common\models\ActiveRecord
{
	const SCANNED = "scanned";
	const NOT_SCANNED = "not-scanned";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_inbound_data_matrix';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inbound_id', 'inbound_item_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['data_matrix_code'], 'string'],
            [['product_barcode', 'product_model'], 'string', 'max' => 36],
            [['status'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'inbound_id' => Yii::t('app', 'ИД приходной накладной'),
            'inbound_item_id' => Yii::t('app', 'ИД строки в приходной накладной'),
            'product_barcode' => Yii::t('app', 'Шк товара'),
            'product_model' => Yii::t('app', 'Модель товара'),
            'data_matrix_code' => Yii::t('app', 'код дата матрицы'),
            'status' => Yii::t('app', 'scanned'),
            'created_user_id' => Yii::t('app', 'Created user id'),
            'updated_user_id' => Yii::t('app', 'Updated user id'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}
