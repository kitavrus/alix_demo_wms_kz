<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_outbound_list".
 *
 * @property int $id
 * @property int $our_outbound_id Our outbound id
 * @property string $client_order_number
 * @property string $ttn_delivery_company
 * @property string $list_title List title
 * @property string $status Status
 * @property string $package_barcode Package barcode
 * @property string $courier_company Courier company
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 * @property string $cargo_company_ttn
 */
class EcommerceOutboundList  extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_outbound_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status','our_outbound_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['list_title', 'package_barcode'], 'string', 'max' => 36],
            [['client_order_number', 'ttn_delivery_company'], 'string', 'max' => 256],
            [['courier_company'], 'string', 'max' => 36],
            [['cargo_company_ttn'], 'string', 'max' => 36],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'our_outbound_id' => 'Our Outbound ID',
            'client_order_number' => 'Client order number',
            'ttn_delivery_company' => 'Ttn delivery company',
            'list_title' => 'List Title',
            'package_barcode' => 'Package Barcode',
            'status' => 'Status',
            'courier_company' => 'Courier company',
            'cargo_company_ttn' => 'cargo company ttn',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}