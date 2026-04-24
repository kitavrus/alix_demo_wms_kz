<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\inbound\domain;

use Yii;

/**
 * This is the model class for table "inbound_data_matrix".
 *
 * @property int $id
 * @property string $inbound_id
 * @property string $inbound_item_id
 * @property string $product_barcode
 * @property string $product_model
 * @property string $data_matrix_code
 * @property string $status
 * @property string $print_status
 * @property int $created_user_id
 * @property int $updated_user_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 */
class InboundDataMatrix extends \common\models\ActiveRecord
{
    const PRINT_STATUS_NO = "no";
    const PRINT_STATUS_YES = "yes";
    const SCANNED = "scanned";
    const NOT_SCANNED = "not-scanned";

    public static function tableName()
    {
        return 'inbound_data_matrix';
    }

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

    public static function find()
    {
        return new InboundDataMatrixQuery(get_called_class());
    }
}
