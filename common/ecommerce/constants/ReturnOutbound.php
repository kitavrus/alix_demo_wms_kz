<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 24.07.2019
 * Time: 19:59
 */

namespace common\ecommerce\constants;


use yii\helpers\ArrayHelper;

class ReturnOutbound
{
    /*
     * @var status
     * */
    const NOT_SET = 0;// Статус не определен
    const FIRS_QUALITY = 1; //- Нет на стоке
    const DONATION = 2; //  Отказ клиента
    //
    const FIRS_QUALITY_VALUE = 'FirstQuality';// or Donation
    const DONATION_VALUE = 'Donation';

    /**
     * @return array Массив с статусами.
     */
    public static function getAllFull()
    {
        return [
            self::NOT_SET => 'NOT_SET', //\Yii::t('stock/titles', 'Не указано'),
            self::FIRS_QUALITY => 'FIRS_QUALITY', //\Yii::t('stock/titles', 'Нет на стоке'),
            self::DONATION => 'DONATION', //\Yii::t('stock/titles', 'Клиент отказался'),
        ];
    }

    /**
     * @return array Массив с статусами.
     */
    public static function getAll()
    {
        return [
            self::FIRS_QUALITY => 'Нормальный товар',
            self::DONATION => 'Бракованный товар',
        ];
    }

    /**
     * @param string $status Читабельный статус поста.
     * @return string
     */
    public static function getValue($status = null)
    {
        return ArrayHelper::getValue(self::getAllFull(), $status);
    }

    /**
     * @param string $status Читабельный статус поста.
     * @return string
     */
    public static function getValueForPartReReserved($status = null)
    {
        return ArrayHelper::getValue(self::getAll(), $status);
    }

    public function convertConditionTypeToAPIValue ($conditionType) {
        $result = self::FIRS_QUALITY_VALUE;
        switch($conditionType) {
            case StockConditionType::UNDAMAGED :
                $result = self::FIRS_QUALITY_VALUE;
                break;
            case StockConditionType::FULL_DAMAGED :
                $result = self::DONATION_VALUE;
                break;
        }

        return $result;
    }
}