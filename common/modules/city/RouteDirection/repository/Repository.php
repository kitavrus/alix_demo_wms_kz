<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 08.09.2017
 * Time: 19:14
 */

namespace common\modules\city\RouteDirection\repository;


use common\modules\city\models\RouteDirections;
use common\modules\city\models\RouteDirectionToCity;
use common\modules\client\models\Client;
use common\modules\store\models\Store;

class Repository
{
    public function getCityIDsByDirectionId($routeDirectionID) {
        $routeDirection = RouteDirections::find()->andWhere(['id'=>$routeDirectionID])->one();
        if($routeDirection) {
            return $routeDirection->getCityIDs();
        }
        return [-1];
    }

    /*
     * @return array [routeDirectionName][storeIds]
     * */
    public function getDirectionStoresIDsGroupType() {
        $routeDirections = RouteDirections::find()->all();

        $routeDirectionsWithStores = [];
        foreach($routeDirections as $routeDirection) {
            $routeDirectionsWithStores[$routeDirection->name] = Store::find()
                                                                    ->select('id')
                                                                    ->andWhere([
                                                                        'type_use'=>Store::TYPE_USE_STORE,
                                                                        'status'=>Store::STATUS_ACTIVE,
                                                                        'client_id'=>Client::CLIENT_DEFACTO,
                                                                        'city_id'=>$this->getCityIDsByDirectionId($routeDirection->id)
                                                                    ])->column();
//                                                                    ->asArray()
//                                                                    ->all();
        }

        return $routeDirectionsWithStores;
    }
    // 8 Алматы
    public function isAlmatyStore($storeId,$clientId) {
        return in_array($storeId, $this->getActiveStoreIDsByClient(8,$clientId));
    }
    // 4 ЮГ
    public function isSouthStore($storeId,$clientId){
        return in_array($storeId,$this->getActiveStoreIDsByClient(4,$clientId));
    }
    // 5 Север
    public function isNorthStore($storeId,$clientId) {
        return in_array($storeId,$this->getActiveStoreIDsByClient(5,$clientId));
    }
    // 6 Запад
    public function isWestStore($storeId,$clientId) {
        return in_array($storeId,$this->getActiveStoreIDsByClient(6,$clientId));
    }
    //7	Восток
    public function isEasternStore($storeId,$clientId) {
        return in_array($storeId,$this->getActiveStoreIDsByClient(7,$clientId));
    }

    public function getActiveStoreIDsByClient($directionId,$clientId) {
        return Store::find()
            ->select('id')
            ->andWhere([
                'type_use'=>Store::TYPE_USE_STORE,
                'status'=>Store::STATUS_ACTIVE,
                'client_id'=>$clientId,
                'city_id'=>$this->getCityIDsByDirectionId($directionId)
            ])->column();
    }
}