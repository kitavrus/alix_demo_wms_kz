<?php
namespace common\ecommerce\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_picking_list".
 *
 * @property int $id
 * @property int $employee_id Employee id
 * @property int $outbound_id Outbound  id
 * @property int $page_number Page number
 * @property int $page_total Page total
 * @property int $status Status
 * @property string $barcode List barcode
 * @property int $begin_datetime The start time of the picking order
 * @property int $end_datetime The end time of the picking order
 * @property int $client_Priority Client Priority
 * @property string $client_ShippingCountryCode Client Shipping Country Code
 * @property string $client_ShippingCity Client Shipping City
 * @property string $client_PackMessage client Client Pack Message
 * @property string $client_GiftWrappingMessage Client Gift Wrapping Message
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommercePickingList extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_picking_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_Priority','employee_id', 'outbound_id', 'page_number', 'page_total', 'status', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['client_ShippingCountryCode','barcode'], 'string', 'max' => 64],
            [['client_PackMessage','client_GiftWrappingMessage'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'employee_id' => Yii::t('app', 'Employee ID'),
            'outbound_id' => Yii::t('app', 'Outbound ID'),
            'page_number' => Yii::t('app', 'Page Number'),
            'page_total' => Yii::t('app', 'Page Total'),
            'status' => Yii::t('app', 'Status'),
            'barcode' => Yii::t('app', 'Barcode'),
            'begin_datetime' => Yii::t('app', 'Begin Datetime'),
            'end_datetime' => Yii::t('app', 'End Datetime'),
            'client_Priority' => Yii::t('app', 'Priority'),
            'client_ShippingCountryCode' => Yii::t('app', 'Shipping Country Code'),
            'client_PackMessage' => Yii::t('app', 'Pack Message'),
            'client_GiftWrappingMessage' => Yii::t('app', 'Gift Wrapping Message'),


            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}
