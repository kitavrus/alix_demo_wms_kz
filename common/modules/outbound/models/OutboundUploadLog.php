<?php

namespace common\modules\outbound\models;

use Yii;

/**
 * This is the model class for table "outbound_upload_log".
 *
 * @property integer $id
 * @property integer $client_id
 * @property string $unique_key
 * @property string $party_number
 * @property string $order_number
 * @property string $product_barcode
 * @property string $product_model
 * @property integer $expected_qty
 * @property integer $from_point_id
 * @property integer $to_point_id
 * @property string $to_point_title
 * @property string $from_point_title
 * @property integer $order_type
 * @property integer $delivery_type
 * @property string $data_created_on_client
 * @property string $extra_fields
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class OutboundUploadLog extends \common\models\ActiveRecord
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
        return 'outbound_upload_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_type','client_id', 'expected_qty', 'from_point_id', 'to_point_id', 'order_type', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['extra_fields'], 'string'],
            [['unique_key', 'party_number', 'order_number', 'product_barcode', 'product_model', 'to_point_title', 'from_point_title'], 'string', 'max' => 34],
            [['data_created_on_client'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('outbound/forms', 'ID'),
            'client_id' => Yii::t('outbound/forms', 'Client ID'),
            'unique_key' => Yii::t('outbound/forms', 'Unique Key'),
            'party_number' => Yii::t('outbound/forms', 'Parent order number'),
            'order_number' => Yii::t('outbound/forms', 'Order number'),
            'product_barcode' => Yii::t('outbound/forms', 'Product Barcode'),
            'product_model' => Yii::t('outbound/forms', 'Product Model'),
            'expected_qty' => Yii::t('outbound/forms', 'Expected Qty'),
            'from_point_id' => Yii::t('outbound/forms', 'From Point ID'),
            'to_point_id' => Yii::t('outbound/forms', 'To Point ID'),
            'to_point_title' => Yii::t('outbound/forms', 'To point title'),
            'from_point_title' => Yii::t('outbound/forms', 'From Point Title'),
            'order_type' => Yii::t('outbound/forms', 'Order Type'),
            'delivery_type' => Yii::t('outbound/forms', 'Delivery_type'),
            'data_created_on_client' => Yii::t('outbound/forms', 'Data Created On Client'),
            'extra_fields' => Yii::t('outbound/forms', 'Extra Fields'),
            'created_user_id' => Yii::t('outbound/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('outbound/forms', 'Updated User ID'),
            'created_at' => Yii::t('outbound/forms', 'Created At'),
            'updated_at' => Yii::t('outbound/forms', 'Updated At'),
            'deleted' => Yii::t('outbound/forms', 'Deleted'),
        ];
    }
}