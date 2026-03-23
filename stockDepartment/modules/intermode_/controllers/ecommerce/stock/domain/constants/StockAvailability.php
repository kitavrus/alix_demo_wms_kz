<?php
namespace app\modules\intermode\controllers\ecommerce\stock\domain\constants;


use yii\helpers\ArrayHelper;

class StockAvailability
{
    const NOT_SET = 0; // Not set
    const NO = 1; // НЕ ДОСТУПНО для резервирования
    const YES = 2; // ДОСТУПНО для резервирования
    const RESERVED = 3; // ЗАРЕЗЕРВИРОВАН
    const BLOCKED = 4; // ЗАБЛОКИРОВАН недоступен для резервирования. Примеры, товар поврежден или потерян
//    const TEMPORARILY_RESERVED = 5; // Частично зарезервирована. Это нужно для Солинс

    /*
   * Availability status array
   * return mixed
   **/
    public static function getConditionTypeArray()
    {
        $data = [
            self::NOT_SET => \Yii::t('stock/titles', 'Не указан'),
            self::NO => \Yii::t('stock/titles', 'НЕ ДОСТУПНО для резервирования'),
            self::YES => \Yii::t('stock/titles', 'ДОСТУПНО для резервирования'),
            self::RESERVED => \Yii::t('stock/titles', 'ЗАРЕЗЕРВИРОВАН'),
            self::BLOCKED => \Yii::t('stock/titles', 'ЗАБЛОКИРОВАН'),
//            self::TEMPORARILY_RESERVED => \Yii::t('stock/titles', 'Частично зарезервирована'),
        ];

        return $data;
    }
    /**
     * @param string $status Читабельный статус поста.
     * @return string
     */
    public static function getValue($status = null)
    {
        return ArrayHelper::getValue(self::getConditionTypeArray(), $status);
    }
}