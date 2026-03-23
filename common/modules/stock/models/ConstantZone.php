<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 27.07.2017
 * Time: 8:28
 */

namespace common\modules\stock\models;


use common\overloads\ArrayHelper;

class ConstantZone
{
    const STATUS_NOT_SET = -1; // 'не установлен',

    const STATUS_PROBLEM = 0; // 'проблема',
    const STATUS_NEW = 1; //'новая'
    const STATUS_IN_WORKING = 2; //'в работе'
    const STATUS_RESERVED = 3; //'подобрана'
    const STATUS_COMPLETE = 4; //'исполнена'
    const STATUS_CANCEL = 5; //'отменена'

    const CATEGORY_A = 0;  // товары категории А
    const CATEGORY_B = 1;  // товары категории Б
    const CATEGORY_VV = 2; // товары  категории ВB
    const CATEGORY_RETURN = 3;      // возвраты
    const CATEGORY_FUNDS = 4;       // фонды
    const CATEGORY_UNADAPTED = 5;   // неадаптированные

    /**
     * @return string Читабельный текст зоны
     */
    public static function getZoneValue($zone = null)
    {
        return ArrayHelper::getValue(self::getZoneArray(), $zone);
    }
    /**
     * @return array Массив с зонами.
     */
    public static function getZoneArray()
    {
        return [
            ConstantZone::CATEGORY_A => \Yii::t('stock/titles', 'категория А'),
            ConstantZone::CATEGORY_B => \Yii::t('stock/titles', 'товары категория Б'),
            ConstantZone::CATEGORY_VV => \Yii::t('stock/titles', 'категория ВB'),
            ConstantZone::CATEGORY_RETURN => \Yii::t('stock/titles', 'возвраты'),
            ConstantZone::CATEGORY_FUNDS => \Yii::t('stock/titles', 'фонды'),
            ConstantZone::CATEGORY_UNADAPTED => \Yii::t('stock/titles', 'неадаптированные'),
        ];
    }
}