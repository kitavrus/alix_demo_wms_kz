<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.01.2019
 * Time: 19:14
 */

namespace common\components\FailDeliveryStatus;

use yii;

class StatusList
{
    const ROAD_CLOSE = 'ROAD_CLOSE';
    const DAMAGE_CAR = 'DAMAGE_CAR';
    const STORE_WITHOUT_ORDER = 'STORE_WITHOUT_ORDER';
    const NO_FREE_WAGGON = 'NO_FREE_WAGGON';
    const NO_FREE_CAR = 'NO_FREE_CAR';
    const OTHER_REASON = 'OTHER_REASON';

    public static function getList() {
        return [
            self::ROAD_CLOSE => Yii::t('FailDeliveryStatus/text','ROAD_CLOSE'),
            self::DAMAGE_CAR => Yii::t('FailDeliveryStatus/text','DAMAGE_CAR'),
            self::STORE_WITHOUT_ORDER => Yii::t('FailDeliveryStatus/text','STORE_WITHOUT_ORDER'),
            self::NO_FREE_WAGGON => Yii::t('FailDeliveryStatus/text','NO_FREE_WAGGON'),
            self::NO_FREE_CAR => Yii::t('FailDeliveryStatus/text','NO_FREE_CAR'),
            self::OTHER_REASON => Yii::t('FailDeliveryStatus/text','OTHER_REASON'),
        ];
    }

    public static function getValue($data) {
        $std = new \stdClass();
        $std->status = '';
        $std->statusText = '';
        $std->otherStatus = '';
        $std->otherStatusText = '';

        if(!empty($data)) {
            $statusList  = yii\helpers\Json::decode($data);
            if(!empty($statusList) && is_array($statusList)) {
                $std->status = yii\helpers\ArrayHelper::getValue($statusList,'status');
                $std->otherStatus = yii\helpers\ArrayHelper::getValue($statusList,'otherStatus');
                $std->statusText = yii\helpers\ArrayHelper::getValue(self::getList(), $std->status);
            }
        }
        return $std;
    }
}