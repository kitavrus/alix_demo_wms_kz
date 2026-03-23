<?php

namespace common\modules\transportLogistics\models;

use common\modules\crossDock\models\CrossDock;
use Yii;
use yii\helpers\VarDumper;
use common\models\ActiveRecord;
use app\modules\transportLogistics\transportLogistics;
use common\modules\client\models\Client;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\outbound\models\OutboundOrder;


/**
 * This is the model class for table "tl_delivery_proposal_orders".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $tl_delivery_proposal_id
 * @property integer $order_type
 * @property integer $delivery_type
 * @property integer $order_id
 * @property string  $order_number
 * @property string  $title
 * @property string  $description
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
class TlDeliveryProposalOrders extends ActiveRecord
{

    /*
     * @var integer order type
     * */
    const ORDER_TYPE_UNDEFINED = 0; //не указан
    const ORDER_TYPE_RPT = 1; // RPT
    const ORDER_TYPE_CROSS_DOCK = 2; // CROSS-DOCK
    const ORDER_TYPE_TRANSFER = 3; // TRANSFER

    /*
    * @var integer delivery type
    * */
    const DELIVERY_TYPE_UNDEFINED = 0; // не указан
    const DELIVERY_TYPE_INBOUND = 1; // приходная накладная
    const DELIVERY_TYPE_OUTBOUND = 2; /// расходная накладная

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposal_orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_type', 'number_places', 'number_places_actual', 'status', 'client_id', 'tl_delivery_proposal_id', 'order_type', 'order_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['order_number', 'title', 'description'], 'string'],
            [['mc', 'mc_actual', 'kg', 'kg_actual'], 'number'],
            [['delivery_type', 'number_places', 'order_number', 'order_type'], 'required'],
        ];
    }

    /*
    *
    * */
    public function scenarios()
    {
        return [
            'default' => ['tl_delivery_proposal_id', 'delivery_type', 'number_places', 'order_number', 'mc', 'kg', 'order_number', 'order_type', 'client_id', 'description', 'title'],
            'create-update-manager-warehouse' => [
                'delivery_type',
                'number_places',
                'order_number',
                'mc',
                'kg',
                'order_number',
                'client_id',
                'order_type',
                'tl_delivery_proposal_id',
                'description',
                'title',
            ],
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
            'tl_delivery_proposal_id' => Yii::t('transportLogistics/forms', 'Tl Delivery Proposal ID'),
            'delivery_type' => Yii::t('transportLogistics/forms', 'Delivery type'),
            'order_type' => Yii::t('transportLogistics/forms', 'Order Type'),
            'order_id' => Yii::t('transportLogistics/forms', 'Order ID'),
            'title' => Yii::t('outbound/forms', 'Title'),
            'description' => Yii::t('outbound/forms', 'Description'),
            'order_number' => Yii::t('transportLogistics/forms', 'Order Number'),
            'number_places' => Yii::t('transportLogistics/forms', 'Number of places'),
            'number_places_actual' => Yii::t('transportLogistics/forms', 'Number of places scanned'),
            'mc' => Yii::t('transportLogistics/forms', 'Mc'),
            'mc_actual' => Yii::t('transportLogistics/forms', 'Mc Actual'),
            'kg' => Yii::t('transportLogistics/forms', 'Kg'),
            'kg_actual' => Yii::t('transportLogistics/forms', 'Kg Actual'),
            'status' => Yii::t('transportLogistics/forms', 'Status'),
            'created_user_id' => Yii::t('transportLogistics/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('transportLogistics/forms', 'Updated User ID'),
            'created_at' => Yii::t('transportLogistics/forms', 'Created At'),
            'updated_at' => Yii::t('transportLogistics/forms', 'Updated At'),
        ];
    }

    /*
    *
    * */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /*
   *
   * */
    public function getOutboundOrder()
    {
        return $this->hasOne(OutboundOrder::className(), ['id' => 'order_id']);
    }

    /*
     *Relation has one with CrossDock
     **/
    public function getCrossDockOrder()
    {
        return $this->hasOne(CrossDock::className(), ['id' => 'order_id']);
    }

    /*
     *Relation has one with DeliveryProposal
     **/
    public function getDeliveryProposal()
    {
        return $this->hasOne(TlDeliveryProposal::className(), ['id' => 'tl_delivery_proposal_id']);
    }


    /*
    * Relation has many with DeliveryRoutes
    * */
    public function getExtras()
    {
        return $this->hasMany(TlDeliveryProposalOrderExtras::className(), ['tl_delivery_proposal_order_id' => 'id']);
    }

    /**
     * @return array.
     */
    public static function getOrderTypeArray()
    {
        return [
            self::ORDER_TYPE_UNDEFINED => Yii::t('forms', 'Undefined'), //Не определен
            self::ORDER_TYPE_RPT => Yii::t('forms', 'RPT'),
            self::ORDER_TYPE_CROSS_DOCK => Yii::t('forms', 'CROSS-DOCK'),
            self::ORDER_TYPE_TRANSFER => Yii::t('forms', 'TRANSFER'),
        ];
    }

    /**
     * @return string .
     */
    public static function getOrderTypeValue($key = 0)
    {
        $data = self::getOrderTypeArray();
        return isset($data[$key]) ? $data[$key] : '-';
    }

    /**
     * @return array.
     */
    public static function getDeliveryTypeArray()
    {
        return [
            self::DELIVERY_TYPE_UNDEFINED => Yii::t('forms', 'Undefined'), //Не определен
            self::DELIVERY_TYPE_INBOUND => Yii::t('forms', 'Inbound'), //Приходная накладная
            self::DELIVERY_TYPE_OUTBOUND => Yii::t('forms', 'Outbound'), //Расходная накладная
        ];
    }

    /**
     * @return string .
     */
    public static function getDeliveryTypeValue($key = 0)
    {
        $data = self::getDeliveryTypeArray();
        return isset($data[$key]) ? $data[$key] : '-';
    }

    /*
     * After save add order to route order
     * */
    public function afterSave($insert, $changedAttributes)
    {

//        $deliveryProposal = TlDeliveryProposal::findOne($this->tl_delivery_proposal_id);
//        VarDumper::dump($this,10,true);
//        VarDumper::dump($deliveryProposal,10,true);
//        die('--0-0-0-0-0-0');
//        if($pRoutes = $deliveryProposal->GetProposalRoutes()->all()) {
//            foreach($pRoutes as $route) {
//                if(!TlDeliveryRouteOrders::find()->where([
//                    'tl_delivery_proposal_id'=>$route->tl_delivery_proposal_id,
//                    'tl_delivery_route_id'=>$route->id,
////                    'order_id'=>$this->order_id,
//                    'order_number'=>$this->order_number,
//                ])->count()) {
//                    $dpRouteOrder = new TlDeliveryRouteOrders();
//                    $dpRouteOrder->tl_delivery_proposal_id = $route->tl_delivery_proposal_id;
//                    $dpRouteOrder->tl_delivery_route_id = $route->id;
//                    $dpRouteOrder->client_id = $this->client_id;
//                    $dpRouteOrder->order_type = $this->order_type;
//                    $dpRouteOrder->order_id = $this->order_id;
//                    $dpRouteOrder->order_number = $this->order_number;
//                    $dpRouteOrder->number_places = $this->number_places;
//                    $dpRouteOrder->mc = $this->mc;
//                    $dpRouteOrder->mc_actual = $this->mc_actual;
//                    $dpRouteOrder->kg = $this->kg;
//                    $dpRouteOrder->kg_actual = $this->kg_actual;
//                    $dpRouteOrder->number_places_actual = $this->number_places_actual;
//                    $dpRouteOrder->save(false);
//                }
//            }
//        }
//        TlDeliveryProposal::recalculateExpensesOrder($deliveryProposal->id);

//        if ($deliveryProposal = TlDeliveryProposal::findOne($this->tl_delivery_proposal_id)) {
//            $deliveryProposal->save(false);
//        }
    }

    /*
    * This method is called at the beginning of inserting or updating a record.
    * @param bool $insert
    * @return bool
    **/
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $orderClass = '';
            if ($this->order_type == self::ORDER_TYPE_CROSS_DOCK) {
                $orderClass = CrossDock::className();
            } else {
                $orderClass = OutboundOrder::className();
            }

            if ($orderClass) {
                if ($order = $orderClass::find()->andWhere(['order_number' => $this->order_number])->one()) {
                    $this->order_id = $order->id;
                }
            }
            return true;
        } else {
            return false;
        }
    }
}
