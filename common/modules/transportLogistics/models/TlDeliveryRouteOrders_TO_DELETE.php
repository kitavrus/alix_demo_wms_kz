<?php

namespace common\modules\transportLogistics\models;

use Yii;
use common\models\ActiveRecord;
use app\modules\transportLogistics\transportLogistics;
use common\modules\Client\models\Client;

/**
 * This is the model class for table "tl_delivery_route_orders".
 *
 * @property integer $id
 * @property integer $tl_delivery_route_id
 * @property integer $tl_delivery_proposal_id
 * @property integer $client_id
 * @property integer $order_type
 * @property integer $order_id
 * @property string  $order_number
 * @property integer $number_places
 * @property integer $number_places_actual
 * @property integer $mc
 * @property integer $mc_actual
 * @property integer $kg
 * @property integer $kg_actual
 * @property integer $status
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class TlDeliveryRouteOrders extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposal_route_orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tl_delivery_route_id', 'tl_delivery_proposal_id', 'client_id', 'order_type', 'order_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['order_number'], 'string'],
            [['mc','mc_actual','kg','kg_actual'], 'number'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('transportLogistics/forms', 'ID'),
            'tl_delivery_route_id' => Yii::t('transportLogistics/forms', 'Tl Delivery Route ID'),
            'tl_delivery_proposal_id' => Yii::t('transportLogistics/forms', 'Tl Delivery Proposal ID'),
            'client_id' => Yii::t('transportLogistics/forms', 'Client ID'),
            'order_type' => Yii::t('transportLogistics/forms', 'Order Type'),
            'order_id' => Yii::t('transportLogistics/forms', 'Order ID'),

            'order_number' => Yii::t('transportLogistics/forms', 'Order Number'),
            'number_places' => Yii::t('transportLogistics/forms', 'Number places'),
            'number_places_actual' => Yii::t('transportLogistics/forms', 'Number places actual'),
            'mc' => Yii::t('transportLogistics/forms', 'Mc'),
            'mc_actual' => Yii::t('transportLogistics/forms', 'Mc actual'),
            'kg' => Yii::t('transportLogistics/forms', 'Kg'),
            'kg_actual' => Yii::t('transportLogistics/forms', 'Kg actual'),
            'status' => Yii::t('transportLogistics/forms', 'Status'),

            'created_user_id' => Yii::t('transportLogistics/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('transportLogistics/forms', 'Updated User ID'),
            'created_at' => Yii::t('transportLogistics/forms', 'Created At'),
            'updated_at' => Yii::t('transportLogistics/forms', 'Updated At'),
        ];
    }

    /*
    * Relation has one with Client
    * */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }
}
