<?php

namespace common\modules\transportLogistics\models;


use Yii;
use common\models\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\modules\store\models\Store;
use common\modules\client\models\Client;
use app\modules\transportLogistics\transportLogistics;

/**
 * This is the model class for table "transport_logistics_order".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $route_from
 * @property integer $route_to
 * @property integer $delivery_date
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
class TlOrder extends ActiveRecord
{

    /*
     * @var integer status
     * */
    const STATUS_ACTIVE = 0;
    const STATUS_NOT_ACTIVE = 1;
    const STATUS_DELETED = 2;

    /*
     * @var integer status
     *
     * */
    const STATUS_NEW = 0; // новый товар еще не отсканирован в короб
    const STATUS_SCANNED = 1; // товар отсканировали в короб
    const STATUS_PRINTED_PRODUCT_LABELS = 2; // для отсканированных товаров в короб распечатаны ценники
    const STATUS_PRINTED_BOX_LABELS = 3; // распечатаны этикетки для коробов
    const STATUS_PACKED = 4; // после того, как этикетки на короба распечатаны, товары переходят в статус упакован
    const STATUS_SHIPPED_COURIER = 5; // короба с товаром упакованы, этикетки на короба наклеены, и отгружены в курьерскую службу


    /**
     * @return array Массив с статусами.
     */
    public static function getStatusArray()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('transportLogistics/forms', 'Active'),
            self::STATUS_NOT_ACTIVE => Yii::t('transportLogistics/forms', 'Not active'),
            self::STATUS_DELETED => Yii::t('transportLogistics/forms', 'Deleted'),
        ];
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getStatus()
    {
        $status = self::getStatusArray();
        return $status[$this->status];
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_order';
//        return 'transport_logistics_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['client_id', 'route_from', 'route_to', 'delivery_date', 'agent_id', 'sale_for_client', 'comment', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'required'],
            [['client_id', 'route_from', 'route_to', 'cross_doc', 'dc', 'hangers', 'other', 'auto_type', 'angar', 'grzch', 'total_qty', 'agent_id', 'cash_no', 'sale_for_client', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['number_places', 'kg', 'mc', 'price_square_meters', 'price_total', 'costs_region', 'our_profit', 'costs_cache', 'with_vat'], 'number'],
            [['comment'], 'string'],
            [['delivery_date'], 'date'], // yy-mm-dd // ,'format'=>'yy-mm-dd'
//            [['delivery_date'], 'date','format'=>'d.m.y'], // yy-mm-dd // ,'format'=>'yy-mm-dd'
            [['dc'], 'default','value'=>'0'], // yy-mm-dd // ,'format'=>'yy-mm-dd'
            [['hangers'], 'default','value'=>'0'], // yy-mm-dd // ,'format'=>'yy-mm-dd'
            [['other'], 'default','value'=>'0'], // yy-mm-dd // ,'format'=>'yy-mm-dd'
            [['angar'], 'default','value'=>'0'], // yy-mm-dd // ,'format'=>'yy-mm-dd'
            [['grzch'], 'default','value'=>'0'], // yy-mm-dd // ,'format'=>'yy-mm-dd'
            [['total_qty'], 'default','value'=>'0'], // yy-mm-dd // ,'format'=>'yy-mm-dd'
            [['price_square_meters'], 'default','value'=>'0'], // yy-mm-dd // ,'format'=>'yy-mm-dd'
            [['price_total'], 'default','value'=>'0'], // yy-mm-dd // ,'format'=>'yy-mm-dd'
            [['costs_region'], 'default','value'=>'0'], // yy-mm-dd // ,'format'=>'yy-mm-dd'
            [['cash_no'], 'default','value'=>'0'], // yy-mm-dd // ,'format'=>'yy-mm-dd'
            [['sale_for_client'], 'default','value'=>'0'], // yy-mm-dd // ,'format'=>'yy-mm-dd'
            [['our_profit'], 'default','value'=>'0'], // yy-mm-dd // ,'format'=>'yy-mm-dd'
            [['costs_cache'], 'default','value'=>'0'], // yy-mm-dd // ,'format'=>'yy-mm-dd'
            [['with_vat'], 'default','value'=>'0'], // yy-mm-dd // ,'format'=>'yy-mm-dd'
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('transportLogistics/forms', 'ID'),
            'client_id' => Yii::t('transportLogistics/forms', 'Client ID'),
            'route_from' => Yii::t('transportLogistics/forms', 'Route From'),
            'route_to' => Yii::t('transportLogistics/forms', 'Route To'),
            'delivery_date' => Yii::t('transportLogistics/forms', 'Delivery Date'),
            'mc' => Yii::t('transportLogistics/forms', 'Mc'),
            'mc_actual' => Yii::t('transportLogistics/forms', 'Mc Actual'),
            'kg' => Yii::t('transportLogistics/forms', 'kg'),
            'kg_actual' => Yii::t('transportLogistics/forms', 'Kg Actual'),
            'number_places' => Yii::t('transportLogistics/forms', 'Number of places'),
            'number_places_scanned' => Yii::t('transportLogistics/forms', 'Number of places scanned'),
            'cross_doc' => Yii::t('transportLogistics/forms', 'Cross Doc'),
            'dc' => Yii::t('transportLogistics/forms', 'Dc'),
            'hangers' => Yii::t('transportLogistics/forms', 'Hangers'),
            'other' => Yii::t('transportLogistics/forms', 'Other'),
            'auto_type' => Yii::t('transportLogistics/forms', 'Auto Type'),
            'angar' => Yii::t('transportLogistics/forms', 'Angar'),
            'grzch' => Yii::t('transportLogistics/forms', 'Grzch'),
            'total_qty' => Yii::t('transportLogistics/forms', 'Total Qty'),
            'price_square_meters' => Yii::t('transportLogistics/forms', 'Price Square Meters'),
            'price_total' => Yii::t('transportLogistics/forms', 'Price Total'),
            'costs_region' => Yii::t('transportLogistics/forms', 'Costs Region'),
            'agent_id' => Yii::t('transportLogistics/forms', 'Agent ID'),  //
            'cash_no' => Yii::t('transportLogistics/forms', 'Cash No'),
            'sale_for_client' => Yii::t('transportLogistics/forms', 'Sale For Client'),
            'our_profit' => Yii::t('transportLogistics/forms', 'Our Profit'),
            'costs_cache' => Yii::t('transportLogistics/forms', 'Costs Cache'),
            'with_vat' => Yii::t('transportLogistics/forms', 'With Vat'),
            'status' => Yii::t('transportLogistics/forms', 'Status'),
            'comment' => Yii::t('transportLogistics/forms', 'Comment'),
            'created_user_id' => Yii::t('transportLogistics/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('transportLogistics/forms', 'Updated User ID'),
            'created_at' => Yii::t('transportLogistics/forms', 'Created At'),
            'updated_at' => Yii::t('transportLogistics/forms', 'Updated At'),
        ];
    }

    /*
     *
     * */
    public function getRouteFromTo($client_id = null)
    {
        return \yii\helpers\ArrayHelper::map(Store::find()->orderBy('title')->all(),'id','title');
    }

    /*
     *
     * */
    public function getRouteFrom()
    {
        return $this->hasOne(Store::className(), ['id' => 'route_from']);
    }

    /*
     *
     * */
    public function getRouteTo()
    {
        return $this->hasOne(Store::className(), ['id' => 'route_to']);
    }

    /*
     *
     * */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /**
     * @return array Массив с формами оплаты.
     */
    public static function getPaymentMethodArray()
    {
        return [
            0 => Yii::t('transportLogistics/forms', 'Cash'),
            1 => Yii::t('transportLogistics/forms', 'Charging to account')
        ];
    }

}


class Setup {
    const DATE_FORMAT = 'Y-m-d';
    const DATETIME_FORMAT = 'Y-m-d H:i:s';
    const TIME_FORMAT = 'H:i:s';

    public static function convert($dateStr, $type='date', $format = null) {
        if ($type === 'datetime') {
            $fmt = ($format == null) ? self::DATETIME_FORMAT : $format;
        }
        elseif ($type === 'time') {
            $fmt = ($format == null) ? self::TIME_FORMAT : $format;
        }
        else {
            $fmt = ($format == null) ? self::DATE_FORMAT : $format;
        }
        return \Yii::$app->formatter->asDate($dateStr, $fmt);
    }
}