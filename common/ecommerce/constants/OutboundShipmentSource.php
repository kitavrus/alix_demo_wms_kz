<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 08.05.2020
 * Time: 10:46
 */

namespace common\ecommerce\constants;

use yii\helpers\ArrayHelper;

class OutboundShipmentSource
{
    /*
 * @var status
 * */
    const NOT_SET = 0;// Статус не определен

    const ECP_KZ = 'ECP-KZ';
    const KASPI_KAZAKHISTAN = 'KaspiKazakhistan';
    const LAMODA_KAZAKHISTAN = 'LamodaKazakhistan';

    /**
     * @param string $lang
     * @return array Массив с статусами.
     */
    public static function getAll($lang = null)
    {
        return [
            self::ECP_KZ => \Yii::t('stock/titles', 'ECP-KZ',[],$lang),
            self::KASPI_KAZAKHISTAN => \Yii::t('stock/titles', 'Kaspi Kazakhistan',[],$lang),
            self::LAMODA_KAZAKHISTAN => \Yii::t('stock/titles', 'Lamoda Kazakhistan',[],$lang),
        ];
    }

    /**
     * @param int $status
     * @param string $lang
     * @return string Читабельный статус поста.
     */
    public static function getValue($status = null,$lang = null)
    {
        return ArrayHelper::getValue(self::getAll($lang), $status);
    }
}