<?php

namespace stockDepartment\modules\wms\models\erenRetail;

use Yii;

/**
 * This is the model class for table "inbound_data_matrix".
 *
 * @property int $id
 * @property string $inbound_id ИД приходной накладной
 * @property string $inbound_item_id ИД строки в приходной накладной
 * @property string $product_barcode Шк товара
 * @property string $product_model Модель товара
 * @property string $data_matrix_code код дата матрицы
 * @property string $status scanned
 * @property string $print_status распечатали или нет
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class InboundDataMatrix extends \common\models\ActiveRecord
{
	const PRINT_STATUS_NO = "no";
	const PRINT_STATUS_YES = "yes";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inbound_data_matrix';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data_matrix_code'], 'string'],
            [['created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['inbound_id', 'inbound_item_id', 'product_barcode', 'product_model'], 'string', 'max' => 36],
            [['status'], 'string', 'max' => 256],
            [['print_status'], 'string', 'max' => 255],
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
            'print_status' => Yii::t('app', 'распечатали или нет'),
            'created_user_id' => Yii::t('app', 'Created user id'),
            'updated_user_id' => Yii::t('app', 'Updated user id'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }

    /**
     * @inheritdoc
     * @return InboundDataMatrixQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new InboundDataMatrixQuery(get_called_class());
    }
}
