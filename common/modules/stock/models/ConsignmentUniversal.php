<?php

namespace common\modules\stock\models;

use Yii;

/**
 * This is the model class for table "consignment_universal".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $from_point_id
 * @property string $from_point_client_id
 * @property integer $to_point_id
 * @property string $to_point_client_id
 * @property string $party_number
 * @property integer $order_type
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
 * @property string $comment_created_on_client
 * @property string $comment_internal
 * @property integer $expected_datetime
 * @property integer $data_created_on_client
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property integer $status_notification
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class ConsignmentUniversal extends \common\models\ActiveRecord
{
    /*
    * @var integer STATUS
    * */
    const STATUS_INBOUND_NEW = 0;
    const STATUS_INBOUND_LOADED_FROM_API = 2;
    const STATUS_INBOUND_LOADED_SAVED = 3;
    const STATUS_INBOUND_COMPLETE = 4;
    const STATUS_INBOUND_NOTIFY_IF_NEW_ORDER = 5;


    const STATUS_OUTBOUND_NEW = 10;
    const STATUS_OUTBOUND_LOADED = 11;
    const STATUS_OUTBOUND_SAVED_AND_CREATE_ORDERS = 12;
    const STATUS_OUTBOUND_COMPLETE = 13;

    const STATUS_RETURN_NEW = 20;
    const STATUS_RETURN_LOADED_FROM_API = 21;
    const STATUS_RETURN_LOADED_SAVED = 22;
    const STATUS_RETURN_COMPLETE = 23;
    const STATUS_RETURN_NOTIFY_IF_NEW_ORDER = 24;

    /*
     * @var integer ORDER TYPE
     * */
    const ORDER_TYPE_INBOUND = 1;
    const ORDER_TYPE_OUTBOUND = 2;
    const ORDER_TYPE_RETURN = 3;

    /*
     * status_notification
     * */
    const STATUS_NOTIFICATION_DEFAULT = 0;

    const STATUS_NOTIFICATION_NEW_INBOUND_IN_PROCESS = 1;
    const STATUS_NOTIFICATION_NEW_INBOUND = 2;
    const STATUS_NOTIFICATION_NEW_INBOUND_IS_PREPARED_PROCESS = 3;
    const STATUS_NOTIFICATION_NEW_INBOUND_IS_PREPARED = 4;

    const STATUS_NOTIFICATION_NEW_OUTBOUND_IN_PROCESS = 21;
    const STATUS_NOTIFICATION_NEW_OUTBOUND = 22;


//    const STATUS_NOTIFICATION_NEW = 0;
//    const STATUS_NOTIFICATION_SEND = 1;
//    const STATUS_NOTIFICATION_ERROR = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'consignment_universal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status_notification','client_id', 'from_point_id', 'to_point_id', 'order_type', 'status', 'expected_qty', 'accepted_qty', 'allocated_qty', 'accepted_number_places_qty', 'expected_number_places_qty', 'allocated_number_places_qty', 'expected_datetime', 'data_created_on_client', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['field_extra1','field_extra2','field_extra3','field_extra4','field_extra5','extra_fields', 'comment_created_on_client', 'comment_internal'], 'string'],
            [['from_point_client_id', 'to_point_client_id', 'party_number', 'status_created_on_client'], 'string', 'max' => 128],
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
            'from_point_id' => Yii::t('app', 'From Point ID'),
            'from_point_client_id' => Yii::t('app', 'From Point Client ID'),
            'to_point_id' => Yii::t('app', 'To Point ID'),
            'to_point_client_id' => Yii::t('app', 'To Point Client ID'),
            'party_number' => Yii::t('app', 'Party Number'),
            'order_type' => Yii::t('app', 'Order Type'),
            'status' => Yii::t('app', 'Status'),
            'status_created_on_client' => Yii::t('app', 'Status Created On Client'),
            'expected_qty' => Yii::t('app', 'Expected Qty'),
            'accepted_qty' => Yii::t('app', 'Accepted Qty'),
            'allocated_qty' => Yii::t('app', 'Allocated Qty'),
            'accepted_number_places_qty' => Yii::t('app', 'Accepted Number Places Qty'),
            'expected_number_places_qty' => Yii::t('app', 'Expected Number Places Qty'),
            'allocated_number_places_qty' => Yii::t('app', 'Allocated Number Places Qty'),
            'extra_fields' => Yii::t('app', 'Extra Fields'),
            'comment_created_on_client' => Yii::t('app', 'Comment Created On Client'),
            'comment_internal' => Yii::t('app', 'Comment Internal'),
            'expected_datetime' => Yii::t('app', 'Expected Datetime'),
            'data_created_on_client' => Yii::t('app', 'Data Created On Client'),
            'begin_datetime' => Yii::t('app', 'Begin Datetime'),
            'end_datetime' => Yii::t('app', 'End Datetime'),
            'status_notification' => Yii::t('app', 'Status notification'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return ConsignmentUniversalQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ConsignmentUniversalQuery(get_called_class());
    }

    /*
     * @param integer $is ConsignmentUniversal
     * @param integer $status Outbound
     * @return void
     * */
    public static function setOutboundStatus($id,$status)
    {
       return ConsignmentUniversal::updateAll(['status'=>$status],['id'=>$id]);
    }

    /*
     * @param integer $is ConsignmentUniversal
     * @param integer $status Inbound
     * @return void
     * */
    public static function setInboundStatus($id,$status)
    {
       return ConsignmentUniversal::updateAll(['status'=>$status],['id'=>$id]);
    }

    /*
     * получаем список все заказов на отгрузку по ИД ConsignmentUniversal
     * */
    public function getAPIOutboundParty(){}
    /*
     * Подготавливаем данные для создания и сохранения outbound orders
     * */
    public function preparedAPIOutboundPartyForSaveToConsignmentOutboundOrder(){}
    /*
     * получаем список все заказов на отгрузку по ИД ConsignmentUniversal
     * */
    public function getAPIOutboundOrders(){}
    /*
     * Подготавливаем данные для создания и сохранения outbound orders
     * */
    public function preparedAPIOutboundOrderForSaveToOutboundOrder(){}
    /*
     * получаем список все заказов на отгрузку по ИД ConsignmentUniversal
     * */
    public function getAPIOutboundOrderItems(){}
    /*
     * Подготавливаем данные для создания и сохранения outbound orders items
     * */
    public function preparedAPIOutboundOrderItemForSaveToOutboundOrderItems(){}
}
