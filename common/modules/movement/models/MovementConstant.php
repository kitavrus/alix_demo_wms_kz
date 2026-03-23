<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 01.08.2017
 * Time: 9:26
 */

namespace common\modules\movement\models;


use common\overloads\ArrayHelper;

class MovementConstant
{
    const STATUS_PROBLEM = -1; // 'проблема',
    const STATUS_NEW = 1; //'новая'
    const STATUS_PRINT_PICK_LIST = 2; //'новая'
    const STATUS_IN_WORKING = 3; //'в работе'
    const STATUS_RESERVED = 4; //'подобрана'
    const STATUS_COMPLETE = 5; //'исполнена'
    const STATUS_CANCEL = 6; //'отменена'
    // STATUS FOR PICK LIST STOCK
    const STATUS_PICK_LIST_STOCK_NEW = 0; //'новый'
    const STATUS_PICK_LIST_STOCK_SCANNED = 1; //'отсканированн'
    const STATUS_PICK_LIST_STOCK_COMPLETE = 2; //'выполнен'

    /**
     * @return string Читабельный текст статус
     */
    public static function getStatusValue($status = null)
    {
        return ArrayHelper::getValue(self::getStatusArray(), $status);
    }
    /**
     * @return array Массив с статусами.
     */
    public static function getStatusArray()
    {
        return [
            self::STATUS_NEW => \Yii::t('stock/titles', 'новая'),
            self::STATUS_PRINT_PICK_LIST => \Yii::t('stock/titles', 'Распечатали лист сборки'),
            self::STATUS_IN_WORKING => \Yii::t('stock/titles', 'в работе'),
            self::STATUS_COMPLETE => \Yii::t('stock/titles', 'исполнена'),
            self::STATUS_RESERVED => \Yii::t('stock/titles', 'подобрана'),
            self::STATUS_PROBLEM => \Yii::t('stock/titles', 'проблема'),
        ];
    }
}