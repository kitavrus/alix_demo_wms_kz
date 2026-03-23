<?php

namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_outbound".
 *
 * @property int $id
 * @property int $client_id Client id
 * @property int $responsible_delivery_id Ответственный за доставку
 * @property string $order_number Order number
 * @property string $external_order_number
 * @property int $expected_qty Expected qty
 * @property int $allocated_qty Allocated qty
 * @property int $accepted_qty Accepted qty
 * @property int $place_expected_qty Place expected qty
 * @property int $place_accepted_qty Place accepted qty
 * @property string $mc Mc
 * @property string $kg Kg
 * @property int $status Status
 * @property int $api_status API Status
 * @property string $first_name first_name
 * @property string $middle_name middle_name
 * @property string $last_name last_name
 * @property string $customer_name
 * @property string $phone_mobile1 Phone mobile 1
 * @property string $phone_mobile2 Phone mobile 2
 * @property string $email email
 * @property string $country country
 * @property string $region region
 * @property string $city city
 * @property string $zip_code zip_code
 * @property string $street street
 * @property string $house house
 * @property string $building Корпус
 * @property string $entrance Подъезд
 * @property string $flat Номер квартиры
 * @property string $intercom Домофон
 * @property string $floor Этаж
 * @property int $elevator Лифт
 * @property string $customer_address
 * @property string $customer_comment Комментарий покупателя
 * @property string $ttn Номер транспортной накладной
 * @property int $payment_method Метод оплаты
 * @property int $payment_status Статус оплаты
 * @property int $data_created_on_client Data created on client
 * @property int $print_picking_list_date Print picking list date
 * @property int $begin_datetime Begin scanning datetime
 * @property int $end_datetime End scanning datetime
 * @property int $packing_date Packing date
 * @property string $package_type Packing type
 * @property int $date_left_warehouse Date left warehouse
 * @property int $date_delivered_to_customer Date delivered to customer
 * @property int $client_Priority Client Priority
 * @property string $client_ShippingCountryCode Client Shipping Country Code
 * @property string $client_ShippingCity Client Shipping City
 * @property string $client_PackMessage client Client Pack Message
 * @property string $client_GiftWrappingMessage Client Gift Wrapping Message *
 * @property string $client_CargoCompany
 * @property string $path_to_cargo_label_file
 * @property string $path_to_order_doc
 * @property string $client_TrackingNumber
 * @property string $client_TrackingUrl
 * @property string $client_ReferenceNumber
 * @property string $client_CancelReason
 * @property string $client_StoreName
 * @property string $client_ShipmentSource
 * @property string $total_price
 * @property string $total_price_tax
 * @property string $total_price_discount
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceOutbound extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
//    public function behaviors()
//    {
//        return [];
//    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_outbound';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['api_status','client_Priority','client_id', 'responsible_delivery_id', 'expected_qty', 'allocated_qty', 'accepted_qty', 'place_expected_qty', 'place_accepted_qty', 'status', 'elevator', 'payment_method', 'payment_status', 'data_created_on_client', 'print_picking_list_date', 'begin_datetime', 'end_datetime', 'packing_date', 'date_left_warehouse', 'date_delivered_to_customer', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['mc', 'kg'], 'number'],
            [['customer_comment', 'ttn'], 'string'],
            [['customer_name'], 'string'],
            [['customer_address'], 'string'],
            [['order_number','external_order_number'], 'string', 'max' => 36],
            [['first_name', 'middle_name', 'last_name', 'phone_mobile1', 'phone_mobile2', 'email', 'country', 'region', 'city', 'zip_code', 'street'], 'string', 'max' => 128],
            [['house', 'building', 'entrance', 'flat', 'intercom', 'floor'], 'string', 'max' => 6],
            [['client_ShippingCountryCode'], 'string', 'max' => 64],
            [['client_PackMessage','client_GiftWrappingMessage'], 'string'],
            [['client_CargoCompany'], 'string'],
            [['path_to_cargo_label_file','path_to_order_doc'], 'string'],
            [['client_TrackingNumber'], 'string'],
            [['client_TrackingUrl'], 'string'],
            [['client_ReferenceNumber'], 'string'],
            [['client_CancelReason'], 'string'],
            [['client_StoreName'], 'string'],
            [['total_price'], 'string'],
            [['total_price_tax'], 'string'],
            [['total_price_discount'], 'string'],
            [['package_type'], 'string'],
            [['client_ShipmentSource'], 'string'],
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
            'responsible_delivery_id' => Yii::t('app', 'Responsible Delivery ID'),
            'order_number' => Yii::t('app', 'Order Number'),
            'expected_qty' => Yii::t('app', 'Expected Qty'),
            'allocated_qty' => Yii::t('app', 'Allocated Qty'),
            'accepted_qty' => Yii::t('app', 'Accepted Qty'),
            'place_expected_qty' => Yii::t('app', 'Place Expected Qty'),
            'place_accepted_qty' => Yii::t('app', 'Place Accepted Qty'),
            'mc' => Yii::t('app', 'Mc'),
            'kg' => Yii::t('app', 'Kg'),
            'status' => Yii::t('app', 'Status'),
            'api_status' => Yii::t('app', 'API Status'),
            'first_name' => Yii::t('app', 'First Name'),
            'middle_name' => Yii::t('app', 'Middle Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'phone_mobile1' => Yii::t('app', 'Phone Mobile1'),
            'phone_mobile2' => Yii::t('app', 'Phone Mobile2'),
            'email' => Yii::t('app', 'Email'),
            'country' => Yii::t('app', 'Country'),
            'region' => Yii::t('app', 'Region'),
            'city' => Yii::t('app', 'City'),
            'zip_code' => Yii::t('app', 'Zip Code'),
            'street' => Yii::t('app', 'Street'),
            'house' => Yii::t('app', 'House'),
            'building' => Yii::t('app', 'Building'),
            'entrance' => Yii::t('app', 'Entrance'),
            'flat' => Yii::t('app', 'Flat'),
            'intercom' => Yii::t('app', 'Intercom'),
            'floor' => Yii::t('app', 'Floor'),
            'elevator' => Yii::t('app', 'Elevator'),
            'customer_comment' => Yii::t('app', 'Customer Comment'),
            'ttn' => Yii::t('app', 'Ttn'),
            'payment_method' => Yii::t('app', 'Payment Method'),
            'payment_status' => Yii::t('app', 'Payment Status'),
            'data_created_on_client' => Yii::t('app', 'Data Created On Client'),
            'print_picking_list_date' => Yii::t('app', 'Print Picking List Date'),
            'begin_datetime' => Yii::t('app', 'Begin Datetime'),
            'end_datetime' => Yii::t('app', 'End Datetime'),
            'packing_date' => Yii::t('app', 'Packing Date'),
            'package_type' => Yii::t('app', 'Packing Type'),
            'date_left_warehouse' => Yii::t('app', 'Date Left Warehouse'),
            'date_delivered_to_customer' => Yii::t('app', 'Date Delivered To Customer'),

            'client_Priority' => Yii::t('app', 'Priority'),
            'client_ShippingCountryCode' => Yii::t('app', 'Shipping Country Code'),
            'client_ShippingCity' => Yii::t('app', 'Shipping city'),
            'client_PackMessage' => Yii::t('app', 'Pack Message'),
            'client_GiftWrappingMessage' => Yii::t('app', 'Gift Wrapping Message'),
            'client_CargoCompany' => Yii::t('app', 'Cargo Company'),
            'client_ShipmentSource' => Yii::t('app', 'Shipment Source'),

            'path_to_cargo_label_file' => Yii::t('app', 'Path to cargo label file'),
            'path_to_order_doc' => Yii::t('app', 'Path to order doc'),
            'client_TrackingNumber' => Yii::t('app', 'Client Tracking number'),
            'client_TrackingUrl' => Yii::t('app', 'Client Tracking url'),
            'client_ReferenceNumber' => Yii::t('app', 'Client Reference number'),
            'client_CancelReason' => Yii::t('app', 'Client Cancel reason'),
            'client_StoreName' => Yii::t('app', 'Client Store Name'),

            'total_price' => Yii::t('app', 'Total price'),
            'total_price_tax' => Yii::t('app', 'Total price tax'),
            'total_price_discount' => Yii::t('app', 'Total price discount'),

            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}