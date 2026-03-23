<?php

namespace common\modules\inbound\models;

use Yii;

/**
 * This is the model class for table "inbound_upload_log".
 *
 * @property integer $id
 * @property integer $client_id
 * @property string $unique_key
 * @property string $order_number
 * @property integer $order_type
 * @property integer $delivery_type
 * @property string $product_barcode
 * @property string $product_model
 * @property integer $expected_qty
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class InboundUploadLog extends \common\models\ActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->dbAudit;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inbound_upload_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_type','order_type','client_id', 'expected_qty', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['unique_key', 'order_number', 'product_barcode', 'product_model'], 'string', 'max' => 34]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('inbound/forms', 'ID'),
            'client_id' => Yii::t('inbound/forms', 'Client ID'),
            'unique_key' => Yii::t('inbound/forms', 'Unique Key'),
            'order_type' => Yii::t('inbound/forms', 'Order type'),
            'delivery_type' => Yii::t('inbound/forms', 'Delivery type'),
            'order_number' => Yii::t('inbound/forms', 'Order Number'),
            'product_barcode' => Yii::t('inbound/forms', 'Product Barcode'),
            'product_model' => Yii::t('inbound/forms', 'Product Model'),
            'expected_qty' => Yii::t('inbound/forms', 'Expected Qty'),
            'created_user_id' => Yii::t('inbound/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('inbound/forms', 'Updated User ID'),
            'created_at' => Yii::t('inbound/forms', 'Created At'),
            'updated_at' => Yii::t('inbound/forms', 'Updated At'),
            'deleted' => Yii::t('inbound/forms', 'Deleted'),
        ];
    }
}
