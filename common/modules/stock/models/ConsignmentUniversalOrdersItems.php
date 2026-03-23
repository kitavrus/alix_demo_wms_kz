<?php

namespace common\modules\stock\models;

use Yii;

/**
 * This is the model class for table "consignment_universal_orders_items".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $consignment_universal_id
 * @property integer $consignment_universal_order_id
 * @property integer $inbound_order_item_id
 * @property integer $from_point_id
 * @property string $from_point_client_id
 * @property integer $to_point_id
 * @property string $to_point_client_id
 * @property integer $order_type
 * @property string $order_type_client
 * @property string $party_number
 * @property string $order_number
 * @property string $box_barcode_client
 * @property string $box_barcode
 * @property string $product_barcode
 * @property string $product_id
 * @property string $product_id_on_client
 * @property integer $status
 * @property string $status_created_on_client
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property integer $allocated_qty
 * @property integer $accepted_number_places_qty
 * @property integer $expected_number_places_qty
 * @property integer $allocated_number_places_qty
 * @property string $extra_fields
 * @property string $field_extra1
 * @property string $field_extra2
 * @property string $field_extra3
 * @property string $field_extra4
 * @property string $field_extra5
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class ConsignmentUniversalOrdersItems extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'consignment_universal_orders_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inbound_order_item_id','client_id', 'consignment_universal_id', 'consignment_universal_order_id', 'from_point_id', 'to_point_id', 'order_type', 'status', 'expected_qty', 'accepted_qty', 'allocated_qty', 'accepted_number_places_qty', 'expected_number_places_qty', 'allocated_number_places_qty', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['extra_fields', 'field_extra1', 'field_extra2', 'field_extra3', 'field_extra4', 'field_extra5'], 'string'],
            [['product_id_on_client','from_point_client_id', 'to_point_client_id', 'order_type_client', 'party_number', 'order_number', 'status_created_on_client'], 'string', 'max' => 128],
            [['box_barcode_client', 'box_barcode', 'product_barcode', 'product_id'], 'string', 'max' => 28],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'client_id' => Yii::t('app', 'Client ID'),
            'consignment_universal_id' => Yii::t('app', 'Consignment Universal ID'),
            'consignment_universal_order_id' => Yii::t('app', 'Consignment Universal Order ID'),
            'inbound_order_item_id' => Yii::t('app', 'Inbound order item id'),
            'from_point_id' => Yii::t('app', 'Internal from point id '),
            'from_point_client_id' => Yii::t('app', 'Client from point id '),
            'to_point_id' => Yii::t('app', 'Internal from point id '),
            'to_point_client_id' => Yii::t('app', 'Client from point id '),
            'order_type' => Yii::t('app', 'Order party type: stock, cross-doc, inbound, outbound etc'),
            'order_type_client' => Yii::t('app', 'Order party type from client: stock, cross-doc, inbound, outbound etc'),
            'party_number' => Yii::t('app', 'Party number, received from the client'),
            'order_number' => Yii::t('app', 'Order number, received from the client'),
            'box_barcode_client' => Yii::t('app', 'Box barcode client, received from the client'),
            'box_barcode' => Yii::t('app', 'Box barcode, received from the client'),
            'product_barcode' => Yii::t('app', 'Product barcode, received from the client'),
            'product_id' => Yii::t('app', 'Product id'),
            'product_id_on_client' => Yii::t('app', 'Product id, received from the client'),
            'status' => Yii::t('app', 'Status new, in process, complete, etc'),
            'status_created_on_client' => Yii::t('app', 'Status created on client side'),
            'expected_qty' => Yii::t('app', 'Expected product quantity in party'),
            'accepted_qty' => Yii::t('app', 'Accepted product quantity in party'),
            'allocated_qty' => Yii::t('app', 'Allocated product quantity in party'),
            'accepted_number_places_qty' => Yii::t('app', 'Accepted number places quantity in party'),
            'expected_number_places_qty' => Yii::t('app', 'Expected number places quantity in party'),
            'allocated_number_places_qty' => Yii::t('app', 'Allocated number places quantity in party'),
            'extra_fields' => Yii::t('app', 'Example JSON: order_number, who received order, etc ...'),
            'field_extra1' => Yii::t('app', 'Extra field 1'),
            'field_extra2' => Yii::t('app', 'Extra field 2'),
            'field_extra3' => Yii::t('app', 'Extra field 3'),
            'field_extra4' => Yii::t('app', 'Extra field 4'),
            'field_extra5' => Yii::t('app', 'Extra field 5'),
            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }

    /**
     * @inheritdoc
     * @return ConsignmentUniversalOrdersItemsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ConsignmentUniversalOrdersItemsQuery(get_called_class());
    }
}