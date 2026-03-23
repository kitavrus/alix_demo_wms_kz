<?php

namespace common\modules\transportLogistics\models;

use Yii;
use common\models\ActiveRecord;
use app\modules\transportLogistics\transportLogistics;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "tl_delivery_proposal_routes_car".
 *
 * @property integer $id
 * @property integer $tl_delivery_proposal_route_id
 * @property integer $tl_delivery_proposal_route_cars_id
 * @property integer $tl_delivery_proposal_id
 * @property integer $order_id
 * @property string  $order_number
 * @property string  $mc //TO DELETE
 * @property integer $mc_actual //TO DELETE
 * @property integer $kg //TO DELETE
 * @property integer $kg_actual //TO DELETE
 * @property integer $number_places //TO DELETE
 * @property integer $number_places_actual //TO DELETE
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class TlDeliveryProposalRouteTransport extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposal_route_transport';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tl_delivery_proposal_id','number_places','number_places_actual','order_id','tl_delivery_proposal_route_id', 'tl_delivery_proposal_route_cars_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['mc','mc_actual', 'kg', 'kg_actual',], 'number'],
            [['order_number',], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('transportLogistics/forms', 'ID'),
            'tl_delivery_proposal_route_id' => Yii::t('transportLogistics/forms', 'Tl Delivery Proposal Route ID'),
            'tl_delivery_proposal_route_cars_id' => Yii::t('transportLogistics/forms', 'Tl Delivery Proposal Route Cars ID'),
            'tl_delivery_proposal_id' => Yii::t('transportLogistics/forms', 'Tl Delivery Proposal ID'),
            'order_id' => Yii::t('transportLogistics/forms', 'Order ID'),
            'order_number' => Yii::t('transportLogistics/forms', 'Order Number'),
            'mc' => Yii::t('transportLogistics/forms', 'Mc'),
            'mc_actual' => Yii::t('transportLogistics/forms', 'Mc Actual'),
            'kg' => Yii::t('transportLogistics/forms', 'kg'),
            'kg_actual' => Yii::t('transportLogistics/forms', 'Kg Actual'),
            'number_places' => Yii::t('transportLogistics/forms', 'Number Places'),
            'number_places_actual' => Yii::t('transportLogistics/forms', 'Number Places Actual'),
            'created_user_id' => Yii::t('transportLogistics/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('transportLogistics/forms', 'Updated User ID'),
            'created_at' => Yii::t('transportLogistics/forms', 'Created At'),
            'updated_at' => Yii::t('transportLogistics/forms', 'Updated At'),
        ];
    }

    /*
   * Relation with Client table
   * */
    public function getRouteCar()
    {
        return $this->hasOne(TlDeliveryProposalRouteCars::className(), ['id' => 'tl_delivery_proposal_route_cars_id']);
    }

    /*
* After save add order to route order
* */
//    public function afterSave($insert, $changedAttributes )
//    {
////        TlDeliveryRoutes::recalculateExpensesRoute($this->tl_delivery_proposal_route_id);
////        TlDeliveryProposal::recalculateExpensesOrder($this->tl_delivery_proposal_id);
////        echo $this->tl_delivery_proposal_route_cars_id."<br />";
//        $mc = $kg = 0;
//
//        if( $car = TlDeliveryProposalRouteCars::findOne($this->tl_delivery_proposal_route_cars_id) ) {
//            if($routes = $car->getTransportItems()->all()) {
//                foreach($routes as $route) {
////                    echo "mc_actual : ".$route->mc_actual."<br />";
////                    echo "kg_actual : ".$route->kg_actual."<br />";
//                    $mc += $route->mc_actual;
//                    $kg += $route->kg_actual;
////                    $c++;
//
////                    VarDumper::dump($route,10,true);
//                }
//            }
//                //TODO: пересчет стоимости машины для всех ее маршрутов
////            if($carRoutes = $car->getRoutes()->all()){
////                $routeCount = count($carRoutes);
////                //VarDumper::dump($carRoutes, 10, true);
////                foreach ($carRoutes as $cr){
////                    $cr->price_invoice = $car->price_invoice / $routeCount;
////                    $cr->price_invoice_with_vat = $car->price_invoice_with_vat / $routeCount;
////                    $cr->recalculateExpensesRoute();
////                    $cr->save(false);
////                }
////            }
//            $car->mc_filled = $mc;
//            $car->kg_filled = $kg;
//            $car->save(false);
//
////            echo $mc . "<br />";
////            echo $kg . "<br />";
////            echo $c . "<br />";
////            die('TlDeliveryProposalRouteTransport ----> afterSave');
//        }
//    }

    /*
     *
     * */
    public function afterDelete()
    {
//        TlDeliveryRoutes::recalculateExpensesRoute($this->tl_delivery_route_id);
//        TlDeliveryProposal::recalculateExpensesOrder($this->tl_delivery_proposal_id);
    }

}