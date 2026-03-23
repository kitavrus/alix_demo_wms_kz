<?php

namespace stockDepartment\modules\outbound\models;

use common\modules\outbound\models\OutboundOrder;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "outbound_order_items".
 *
 * @property integer $id
 * @property integer $outbound_order_id
 * @property integer $product_id
 * @property string $product_name
 * @property string $product_barcode
 * @property string $product_price
 * @property string $product_model
 * @property string $product_sku
 * @property string $product_madein
 * @property string $product_composition
 * @property string $product_exporter
 * @property string $product_importer
 * @property string $product_description
 * @property string $product_serialize_data
 * @property string $box_barcode
 * @property integer $status
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class OutboundCreateForm extends OutboundOrder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'delivery_type',
                'consignment_outbound_order_id',
                'allocated_qty',
                'allocated_number_places_qty',
                'from_point_id',
                'to_point_id','client_id',
                'supplier_id',
                'warehouse_id',
                'order_type',
                'status',
                'cargo_status',
                'expected_qty',
                'accepted_qty',
                'accepted_number_places_qty',
                'expected_number_places_qty',
                'expected_datetime',
                'begin_datetime',
                'end_datetime',
                'created_user_id',
                'updated_user_id',
                'created_at',
                'updated_at',
                 'date_confirm',
            ], 'integer'],
            [['mc','kg'], 'number'],
            [['title', 'description'], 'string'],
            [['client_id', 'from_point_id', 'to_point_id'], 'required'],
        ];
    }

}
