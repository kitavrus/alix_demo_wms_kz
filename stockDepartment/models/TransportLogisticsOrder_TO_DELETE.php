<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transport_logistics_order".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $route_from
 * @property integer $route_to
 * @property string $delivery_date
 * @property string $mc
 * @property string $mc_actual
 * @property string $kg
 * @property string $kg_actual
 * @property integer $number_places
 * @property integer $number_places_scanned
 * @property integer $cross_doc
 * @property integer $dc
 * @property integer $hangers
 * @property integer $other
 * @property integer $auto_type
 * @property integer $angar
 * @property integer $grzch
 * @property integer $total_qty
 * @property string $price_square_meters
 * @property string $price_total
 * @property string $costs_region
 * @property integer $agent_id
 * @property integer $cash_no
 * @property integer $sale_for_client
 * @property string $our_profit
 * @property string $costs_cache
 * @property string $with_vat
 * @property integer $status
 * @property string $comment
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class TlOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transport_logistics_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'route_from', 'route_to', 'delivery_date', 'mc_actual', 'kg_actual', 'number_places_scanned', 'agent_id', 'sale_for_client', 'comment', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'required'],
            [['client_id', 'route_from', 'route_to', 'number_places', 'number_places_scanned', 'cross_doc', 'dc', 'hangers', 'other', 'auto_type', 'angar', 'grzch', 'total_qty', 'agent_id', 'cash_no', 'sale_for_client', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['delivery_date'], 'safe'],
            [['mc', 'mc_actual', 'kg', 'kg_actual', 'price_square_meters', 'price_total', 'costs_region', 'our_profit', 'costs_cache', 'with_vat'], 'number'],
            [['comment'], 'string']
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
            'route_from' => Yii::t('app', 'Route From'),
            'route_to' => Yii::t('app', 'Route To'),
            'delivery_date' => Yii::t('app', 'Delivery Date'),
            'mc' => Yii::t('app', 'Mc'),
            'mc_actual' => Yii::t('app', 'Mc Actual'),
            'kg' => Yii::t('app', 'Kg'),
            'kg_actual' => Yii::t('app', 'Kg Actual'),
            'number_places' => Yii::t('app', 'Number Places'),
            'number_places_scanned' => Yii::t('app', 'Number Places Scanned'),
            'cross_doc' => Yii::t('app', 'Cross Doc'),
            'dc' => Yii::t('app', 'Dc'),
            'hangers' => Yii::t('app', 'Hangers'),
            'other' => Yii::t('app', 'Other'),
            'auto_type' => Yii::t('app', 'Auto Type'),
            'angar' => Yii::t('app', 'Angar'),
            'grzch' => Yii::t('app', 'Grzch'),
            'total_qty' => Yii::t('app', 'Total Qty'),
            'price_square_meters' => Yii::t('app', 'Price Square Meters'),
            'price_total' => Yii::t('app', 'Price Total'),
            'costs_region' => Yii::t('app', 'Costs Region'),
            'agent_id' => Yii::t('app', 'Agent ID'),
            'cash_no' => Yii::t('app', 'Cash No'),
            'sale_for_client' => Yii::t('app', 'Sale For Client'),
            'our_profit' => Yii::t('app', 'Our Profit'),
            'costs_cache' => Yii::t('app', 'Costs Cache'),
            'with_vat' => Yii::t('app', 'With Vat'),
            'status' => Yii::t('app', 'Status'),
            'comment' => Yii::t('app', 'Comment'),
            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
