<?php

namespace common\modules\movement\models;

use Yii;
use yii\helpers\ArrayHelper;

class MovementPickListConstant
{
    /*
    * @var integer status
    * */
    const STATUS_NOT_SET = 0; // не указан
    const STATUS_PRINT = 1; // Напечатали лист сборки
    const STATUS_BEGIN = 2; // Начали сборку
    const STATUS_END = 3; // Закончили
    const STATUS_DONE = 4; // Выполнена

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
            self::STATUS_NOT_SET => \Yii::t('stock/titles', 'не указан'),
            self::STATUS_PRINT => \Yii::t('stock/titles', 'Напечатали лист сборки'),
            self::STATUS_BEGIN => \Yii::t('stock/titles', 'Начали сборку'),
            self::STATUS_END => \Yii::t('stock/titles', 'Закончили сборку'),
            self::STATUS_DONE => \Yii::t('stock/titles', 'Выполнена'),
        ];
    }
}