<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.09.2017
 * Time: 15:11
 */

namespace common\modules\store\repository;


use common\modules\client\models\Client;
use common\modules\store\models\Store;
use common\overloads\ArrayHelper;
use yii\helpers\VarDumper;

class Repository
{
    public static function getStoreByIDs($ids) {
       $stores = Store::find()->andWhere(['id'=>$ids])->asArray()->all();
       return ArrayHelper::map($stores,'id',function($item, $default) {
            return $item['city_lat'].' / '.$item['shopping_center_name'];
        });
    }

    public static function getStoreByClient($clientId = 2) {
       $stores = Store::find()->andWhere(['client_id'=>$clientId,'type_use'=>Store::TYPE_USE_STORE])->asArray()->all();
       return ArrayHelper::map($stores,'id',function($item, $default) {
            return $item['city_lat'].' / '.$item['shopping_center_name'];
        });
    }

    public static function getStoreCityNameByClient($clientId = 2) {
       $stores = Store::find()->andWhere(['client_id'=>$clientId,'type_use'=>Store::TYPE_USE_STORE])->asArray()->all();
       return ArrayHelper::map($stores,'id',function($item, $default) {
            return $item['city_lat'].' / '.$item['name'];
        });
    }

    public function getStoreCityNameByClientWithPattern($clientId,$pattern = 'stock') {
       return $this->getStockPointArray($clientId,true,false,$pattern);
    }

    /*
    * Return array available stores
    * @param integer $client_id Client id
    * @param integer $showOnlyStore Show only store. Default value false
    * @return array Stores list [id=>title]
    *
    * */
    public function getStockPointArray($client_id = null, $showOnlyStore = false, $showInactive = false, $titlePattern = 'stock')
    {
        $q = Store::find()->joinWith(['city', 'client']);


        if($showOnlyStore) {
            $q->andFilterWhere(['type_use'=>Store::TYPE_USE_STORE]);
        }
        if(!$showInactive){
            $q->andWhere(['clients.status'=>Client::STATUS_ACTIVE]);
        }


        $q->andFilterWhere(['client_id'=>$client_id]);
        if($client_id) {
            $q->orFilterWhere(['store.id' => [4, 123]]);
        }

//        if($client_id){
//            $q->orWhere(['store.id'=>[4, 123]]); // Наш склад Склад DC
//        }

        $q->orderBy('city.name');

        $data = \yii\helpers\ArrayHelper::map($q->all(),'id', function($m) use ($titlePattern) {
            return $m->getPointTitleByPattern($titlePattern);
        });
        return $data;
    }

    public function getStoreByClientID($clientId = 2) {
      return self::getStoreCityNameByClient($clientId);
    }

    public function getPointNameById($pointId,$pattern = 'stock') {
        $point = Store::findOne($pointId);
        if($point) {
            return $point->getPointTitleByPattern($pattern);
        }
        return 'не найден';
    }

    public function isRusStore($storeId) {
        return Store::find()->andWhere(['country_id'=>3/* Это ид России */,'id'=>$storeId])->exists();
    }
    public function isBelStore($storeId) {
        return Store::find()->andWhere(['country_id'=>2/* Это ид Белоруссии */,'id'=>$storeId])->exists();
    }
    public function isKzStore($storeId) {
        return Store::find()->andWhere(['country_id'=>1/* Это ид Казахстана */,'id'=>$storeId])->exists();
    }

    public function getStoreQueryWithoutRegion($clientId,$withoutRegionId) {
       return Store::find()
                ->select('id')
                ->andWhere([
                    'client_id'=>$clientId,
                    'region_id'=>$withoutRegionId,
                    'type_use'=>Store::TYPE_USE_STORE
                ]);
//                ->asArray()
//                ->all();
    }
}