<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 24.07.2019
 * Time: 19:59
 */

namespace common\ecommerce\constants;


use yii\helpers\ArrayHelper;

class StockOutboundStatus
{
    /*
     * @var status
     * */
    const NOT_SET = 0;// Статус не определен

    const _NEW = 1; // Товары из приходной накладной ЗАГРУЗИЛИ В СИСТЕМУ но ничего с ними не делали (на склад они к нам не прибыли)
    const SCANNING = 3; // НАЧАЛИ СКАНИРОВАТЬ товары из приходной накладной
    const SCANNED = 4; // Товар ОТСКАНИРОВАН из приходной накладной
    const OVER_SCANNED = 5; // ЛИШНИЙ ОТСКАНИРОВАНЫЙ товар в приходной накладной
    const PRINT_BOX_LABEL = 19;   // Распечатали этикетки на короба
    const PRINTING_BOX_LABEL = 20;   // Распечатали этикетки для одного из сборочных листов
    const FULL_RESERVED = 21; //  ПОЛНОСТЬЮ ЗАРЕЗЕРВИРОВАНА
//    const RESERVING = 22; //  В ПРОЦЕССЕ ЗАРЕЗЕРВИРОВАНИЯ
    const PART_RESERVED = 23; //  ЧАСТИЧНО ЗАРЕЗЕРВИРОВАНА
    const PRINTED_PICKING_LIST = 24;
    const DONE = 25;
    const TRANSFER_B2C_TO_B2B = 99; // Этот статус для товаров которые мы перемещаем руками из склада екомерса на b2b склад

    /**
     * @param string $lang
     * @return array Массив с статусами.
     */
    public static function getAll($lang = null)
    {
        return [
            self::_NEW => \Yii::t('stock/titles', 'New',[],$lang),
            self::FULL_RESERVED => \Yii::t('stock/titles', 'Full reserved',[],$lang),//разные
            self::PART_RESERVED => \Yii::t('stock/titles', 'Part reserved',[],$lang),//разные
            self::SCANNING => \Yii::t('stock/titles', 'Scanning',[],$lang),//один
            self::SCANNED => \Yii::t('stock/titles', 'Scanned',[],$lang),//один
            self::DONE => \Yii::t('stock/titles', 'Given to courier',[],$lang),
            self::PRINT_BOX_LABEL => \Yii::t('stock/titles', 'Print box label',[],$lang),
            self::TRANSFER_B2C_TO_B2B => \Yii::t('stock/titles', 'TRANSFER B2C TO B2B',[],$lang),
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

    public static function getReadyForScanning()
    {
        return [
            self::NOT_SET,
            self::_NEW,
            self::PART_RESERVED,
            self::FULL_RESERVED,
            self::PRINTED_PICKING_LIST,
        ];
    }

    public static function getPrintBoxOnStock() {
        return [
            self::SCANNED,
            self::PRINT_BOX_LABEL,
            self::PRINTING_BOX_LABEL,
        ];
    }

    public static function getOrderItemsForDiffReport() {
        return [
            self::SCANNED,
            self::SCANNING
        ];
    }

    public static function getExistInBox() {
        return [
            self::NOT_SET,
            self::_NEW,
            self::FULL_RESERVED,
            self::PART_RESERVED,
            self::PRINTED_PICKING_LIST,
        ];
    }

    public static function getOrderPacked() {
        return [
            self::PRINT_BOX_LABEL,
        ];
    }
}