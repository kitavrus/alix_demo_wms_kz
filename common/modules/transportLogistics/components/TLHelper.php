<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 11.09.14
 * Time: 11:25
 */

namespace common\modules\transportLogistics\components;

use common\modules\client\models\Client;
use common\modules\client\models\ClientEmployees;
use Yii;
use yii\helpers\ArrayHelper;
use common\modules\store\models\Store;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;
use common\modules\city\models\City;
use common\modules\city\models\Country;
use common\modules\city\models\Region;
use common\modules\transportLogistics\models\TlDeliveryRoutes;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use app\modules\transportLogistics\transportLogistics;
use yii\helpers\VarDumper;


class TLHelper {


    /*
    * @var integer invoice status
    * */
    const ORDER_TYPE_UNDEFINED = 0; //Не указан
    const ORDER_TYPE_INBOUND = 1; //Приходная
    const ORDER_TYPE_OUTBOUND = 2; //Расходная


    /*
    * Return array available stores
    * @param integer $client_id Client id
    * @param integer $showOnlyStore Show only store. Default value false
    * @return array Stores list [id=>title]
    *
    * */
    public static function getStoreArrayByClientID($client_id = null, $showOnlyStore = false)
    {

        $q = Store::find();
        $qSub = clone $q;

//        $q->where('1=:a',[':a'=>1]);

//        if(!is_null($client_id)) {
        $q->andFilterWhere(['client_id'=>$client_id]);
//        }

        if($showOnlyStore) {
            $q->andFilterWhere(['type_use'=>Store::TYPE_USE_STORE]);
        }

        $data = ArrayHelper::map($q->with('city','client')->all(),'id',function($m) use ($client_id) {
            //return $m->city->name . ' / ' . $m->name . ' '.(!empty($m->shop_code) ? $m->shop_code : '').' '. ((!empty($m->shopping_center_name) && $m->shopping_center_name != '-')  ? '  [ ТЦ ' . $m->shopping_center_name . ' ] ' : '') . ' / '.$m->street;
                if($client_id == Client::CLIENT_DEFACTO && !empty($m->shopping_center_name_lat)) {

                    return $m->getPointTitleByPattern('{city_name_lat} {shopping_center_name_lat} / {city_name} {shopping_center_name} {name}');
//                    return $m->getPointTitleByPattern('{city_name} / {name} {shop_code} {shopping_center_name_lat} {street} {house}');
                }
                    return $m->getPointTitleByPattern('full');
        });


        $qSub->andFilterWhere(['id'=>[4,123,103]]); // Наш склад Склад DC

        $data += ArrayHelper::map($qSub->with(['city','client'])->all(),'id',function($m) {
            return $m->city->name . ' / ' . $m->name . ' '.(!empty($m->shop_code) ? $m->shop_code : '').' '. ((!empty($m->shopping_center_name) && $m->shopping_center_name != '-')  ? '  [ ТЦ ' . $m->shopping_center_name . ' ] ' : '') . ' / '.$m->street;
        });

        if($client_id == Client::CLIENT_DEFACTO) {
            if ($client = ClientEmployees::findOne(['user_id' => Yii::$app->user->id])) {
                $storeRusRegionIDs = [];
                switch ($client->manager_type) {
                    case ClientEmployees::TYPE_REGIONAL_OBSERVER_RUSSIA:
                        $country_id = 3;
                        $storeRusRegionIDs = Store::find()->select('id')->andWhere(['country_id'=>$country_id])->column();
                        break;
                    case ClientEmployees::TYPE_REGIONAL_OBSERVER_BELARUS:
                        $country_id = 2;
                        $storeRusRegionIDs = Store::find()->select('id')->andWhere(['country_id'=>$country_id])->column();
                        break;
                    default:
                        break;
                }

                if(!empty($storeRusRegionIDs)) {
                    $storeRusRegionIDs[] = 4;
                    $storeRusRegionIDs[] = 123;
                    foreach($data as $storeId=>$storeName) {
                        if(!in_array($storeId,$storeRusRegionIDs)) {
                            unset($data[$storeId]);
                        }
                    }
                }
            }
        }

//        VarDumper::dump($storeRusRegionIDs,10,true);
//        VarDumper::dump($data,10,true);
//        die;
        return $data;

    }

    /*
    * Return array available stores
    * @param integer $client_id Client id
    * @param integer $showOnlyStore Show only store. Default value false
    * @return array Stores list [id=>title]
    * @return array $countryIds
    *
    * */
    public static function getStockPointArray($client_id = null, $showOnlyStore = false, $showInactive = false, $titlePattern = 'stock',$countryIds = [])
    {
        //главный запрос точек
        //$q = Store::find();
        $q = Store::find()->joinWith(['city', 'client']);

        //подзапрос клиентов
//        $subQ = (new \yii\db\Query())
//            ->select(['id'])
//            ->from(Client::tableName())
//            ->where(['deleted' => Client::NOT_SHOW_DELETED])
//            ->andFilterWhere(['id'=>$client_id]);

        if(!$showInactive){
            $q->andWhere(['clients.status'=>Client::STATUS_ACTIVE]);
        }

        $q->andFilterWhere(['client_id'=>$client_id]);



        if($showOnlyStore) {
            $q->andFilterWhere(['type_use'=>Store::TYPE_USE_STORE]);
        }

        if(!empty($countryIds)) {
            $q->andFilterWhere(['country_id'=>$countryIds]);
        }

        if($client_id){
            $q->orWhere(['store.id'=>[4, 20,103]]); // Наш склад Склад DC
        }

        $q->orderBy('city.name');
        $data = ArrayHelper::map($q->all(),'id', function($m) use ($titlePattern) {

            return $m->getPointTitleByPattern($titlePattern);
        });
        return $data;
    }


    /*
     * Get available route car
     * @param $route_from
     * @param $route_to
     * @return array Возвращает список машин котрые едут в заданном направлении и не заполнены польностью
     *
     * */
    public static function getAvailableCarByRoute($route_from,$route_to,$orWhere = [])
    {
        $q = TlDeliveryProposalRouteCars::find();
        $q->where(['route_city_from'=>$route_from,'route_city_to'=>$route_to]);

        if(isset($orWhere[0]) && !empty($orWhere[0])) {
            $q->orWhere(['route_city_from'=>$orWhere[0],'route_city_to'=>$orWhere[1]]);
        }

        return ArrayHelper::map($q->orderBy('route_city_from')->all(),'id',function($car) {
            return $car->routeCityFrom->name . ' => ' . $car->routeCityTo->name .' / '.Yii::t('titles','Car') . ' : '.$car->car->name.'    '.Yii::t('titles','filled ') . ' ' . (!empty($car->mc_filled) ? $car->mc_filled  : '0') . ' М3 / ' . (!empty($car->kg_filled) ? $car->kg_filled :'0').' Кг';
        });
    }


    /*
    * Get available route car
    * @param integer $route_city_from
    * @param integer $route_city_to
    * @param integer $excludeUsedId
    *
    * @return array Возвращает список машин котрые едут в заданном направлении и не заполнены польностью
    *
    * */
    public static function getFreeCarByCity($route_from,$route_to,$excludeUsedId = null)
    {
        $q = TlDeliveryProposalRouteCars::find();
        $q->where(['route_city_from'=>$route_from,'route_city_to'=>$route_to, 'deleted'=>TlDeliveryProposalRouteCars::NOT_SHOW_DELETED, 'status'=>[TlDeliveryProposalRouteCars::STATUS_NEW,TlDeliveryProposalRouteCars::STATUS_FREE,TlDeliveryProposalRouteCars::STATUS_CAR_ADDED_TO_ROUTE,TlDeliveryProposalRouteCars::STATUS_ROUTE_FORMED]]);

        $excludeUsedId = is_array($excludeUsedId) ? $excludeUsedId : [$excludeUsedId];

        $q->andWhere(['NOT IN','id',$excludeUsedId]);

        return ArrayHelper::map($q->orderBy('route_city_from')->all(),'id',function($car) use ($excludeUsedId) {
            return $car->routeCityFrom->name . ' => ' . $car->routeCityTo->name .' / '.($car->car->agent->name).' / '.Yii::t('titles','Car') . ' : '.$car->car->name.' ( '.$car->car->kg.' '.Yii::t('titles','kg') .' / '.$car->car->mc.' '.Yii::t('titles','mc') .' ) /  '.Yii::t('titles','filled ') . ' ' . (!empty($car->mc_filled) ? $car->mc_filled  : '0') . ' М3 / ' . (!empty($car->kg_filled) ? $car->kg_filled :'0').' Кг';
        });
    }


    /*
    * Is available route car
    * @param integer $route_city_from
    * @param integer $route_city_to
    * @param integer $excludeUsedId
    *
    * @return array Возвращает список машин котрые едут в заданном направлении и не заполнены польностью
    *
    * */
    public static function isFreeCarByCity($route_id)
    {
        $modelDpRoute = TlDeliveryRoutes::findOne($route_id);
        $dp_from = $modelDpRoute->getRouteFrom()->one();
        $dp_to = $modelDpRoute->getRouteTo()->one();

        // $route_from = $dp_from->getCity()->one()->id;
        $route_from = null;
        if($dp_from) {
            $route_from = $dp_from->city_id;
        }
        // $route_to = $dp_to->getCity()->one()->id;
        $route_to = null;
        if($dp_to) {
            $route_to = $dp_to->city_id;
        }
        // if($rf = $dp_from->getCity()->one()) {
        // $route_from = $rf->id;
        // }



//        VarDumper::dump($dp_from,10,true);
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        VarDumper::dump($dp_to,10,true);
//        echo $route_from."<br />";
//        echo $route_to."<br />";
        $excludeUsedId = $modelDpRoute->getCarItems()->asArray()->select('id')->column();

        $q = TlDeliveryProposalRouteCars::find();
        $q->where(['route_city_from'=>$route_from,
            'route_city_to'=>$route_to,
            'status'=>[
                TlDeliveryProposalRouteCars::STATUS_NEW,
                TlDeliveryProposalRouteCars::STATUS_FREE,
                TlDeliveryProposalRouteCars::STATUS_ROUTE_FORMED,
                TlDeliveryProposalRouteCars::STATUS_CAR_ADDED_TO_ROUTE,
            ]]);

        $excludeUsedId = is_array($excludeUsedId) ? $excludeUsedId : [$excludeUsedId];

        $q->andWhere(['NOT IN','id',$excludeUsedId]);

        return $q->orderBy('route_city_from')->count();
    }

    /*
     * Get routes by car id
     * @param integer $carID
     *
     * */
    public function getRouteByCarID($carID)
    {


    }


    /*
     * Return array cities ['id'=>'city name']
     * @return array Cities
     * */
    public static function getCityArray()
    {
        return City::getArrayData();
    }

    /*
     * Return array countries ['id'=>'country name']
     * @return array Countries
     * */
    public static function getCountryArray()
    {
        return Country::getArrayData();
    }

    /*
     * Return array regions ['id'=>'region name']
     * @return array Regions
     * */
    public static function getRegionArray()
    {
        return Region::getArrayData();
    }

    /*
     * Return array order type ['id'=>'type name']
     * @return array
     * */
//    public static function getOrderTypeArray($key = null)
//    {
//        $data = [
//            self::ORDER_TYPE_UNDEFINED => Yii::t('transportLogistics/forms', 'Undefined'), //Не определен
//            self::ORDER_TYPE_INBOUND => Yii::t('transportLogistics/forms', 'Inbound'), //Приходная
//            self::ORDER_TYPE_OUTBOUND => Yii::t('transportLogistics/forms', 'Outbound'), //Расходная
//        ];
//        return isset($data[$key]) ? $data[$key] : $data;
//    }


    /*
     * Return string proposal label
     * @param integer $clientId
     * @param integer $ProposalId
     * @return string
     * */
    public static function getProposalLabel($clientId, $proposalId)
    {
        $array = self::getStoreArrayByClientID($clientId);
        $proposal = TlDeliveryProposal::findOne($proposalId);

        if(!empty ($array) && !empty($proposal)){
        $titleFrom = isset($array[$proposal->route_from]) ? $array[$proposal->route_from] : '';
        $titleTo = isset($array[$proposal->route_to]) ? $array[$proposal->route_to] : '';
        }
        return !empty ($titleFrom) && !empty($titleTo) ? $titleFrom . ' => ' .$titleTo: '';
    }


    /*
     * Recalculate DP and Routes
     * @param integer $dpID $delivery_proposal_id
     * @param integer $dprID $delivery_proposal_route_id
     * */
    public static function recalculateDpAndDpr($dpID,$dprID )
    {
        if($dpr = TlDeliveryRoutes::findOne($dprID)) {
            $dpr->recalculateExpensesRoute();
        }

        if($dp = TlDeliveryProposal::findOne($dpID)) {
            $dp->recalculateExpensesOrder();
            $dp->setCascadedStatus();
        }

        return true;
    }
	
    public static function getStoreNameById($storeId)
    {
        $stores = self::getStoreArrayByClientID();
        return $stores[$storeId];
    }
}