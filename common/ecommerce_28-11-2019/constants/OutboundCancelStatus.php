<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 24.07.2019
 * Time: 19:59
 */

namespace common\ecommerce\constants;


use yii\helpers\ArrayHelper;

class OutboundCancelStatus
{
    /*
     * @var status
     * */
    const NOT_SET = 0;// Статус не определен

    const UNABLE_TO_FULFIL = 1; //- Нет на стоке
    const CUSTOMER_REQUESTS_CANCELLATION = 2; //  Отказ клиента
    const UN_COLLECTED = 3; //  Есть в стоке но не нашли
    const FRAUD = 4; //  Это ошибочный заказ или обман
    const ENTER_CANCELLATION_REASON_MANUALLY = 5; // Указываем руками причину отмены
    const DAMAGE_PRODUCT = 'DamageProduct'; // Указываем руками причину отмены
    const PARTIAL_CANCEL = 'PartialCancel'; // частичная отмена если ожидали 4 а отгрузили 3 товара

    /**
     * @return array Массив с статусами.
     */
    public static function getAll()
    {
        return [
            self::NOT_SET => 'NOT_SET', //\Yii::t('stock/titles', 'Не указано'),
            self::UNABLE_TO_FULFIL => 'UNABLE_TO_FULFIL', //\Yii::t('stock/titles', 'Нет на стоке'),
            self::CUSTOMER_REQUESTS_CANCELLATION => 'CUSTOMER_REQUESTS_CANCELLATION', //\Yii::t('stock/titles', 'Клиент отказался'),
            self::UN_COLLECTED => 'UN_COLLECTED', //\Yii::t('stock/titles', 'Есть в стоке но не нашли'),
            self::FRAUD =>  'FRAUD', //\Yii::t('stock/titles', 'Ошибочный заказ или обман'),
            self::ENTER_CANCELLATION_REASON_MANUALLY => 'ENTER_CANCELLATION_REASON_MANUALLY', //\Yii::t('stock/titles', 'Указываем руками причину отмены'),
            self::DAMAGE_PRODUCT => 'DAMAGE_PRODUCT', //\Yii::t('stock/titles', 'В заказе браковынный товар'),
        ];
    }

    /**
     * @param string $status Читабельный статус поста.
     * @return string
     */
    public static function getValue($status = null)
    {
        return ArrayHelper::getValue(self::getAll(), $status);
    }
}