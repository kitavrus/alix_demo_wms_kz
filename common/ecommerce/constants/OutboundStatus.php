<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 24.07.2019
 * Time: 19:59
 */

namespace common\ecommerce\constants;


use yii\helpers\ArrayHelper;

class OutboundStatus
{
    /*
     * @var status
     * */
    const NOT_SET = 0;// Статус не определен

    const _NEW = 1;
    const FULL_RESERVED = 2; //  ПОЛНОСТЬЮ ЗАРЕЗЕРВИРОВАНА
    const RESERVING = 3; //  В ПРОЦЕССЕ ЗАРЕЗЕРВИРОВАНИЯ
    const PART_RESERVED = 4; //  ЧАСТИЧНО ЗАРЕЗЕРВИРОВАНА
    const PRINTED_PICKING_LIST = 5; //  Напечатали лист сборки
    const SCANNING = 6; //  СКАНИРУЕТСЯ
    const SCANNED = 7;  //  ОТСКАНИРОВАН
    const ON_ROAD = 8; //  В ПУТИ
    const DELIVERED = 9; //  ДОСТАВЛЕН
    const DONE = 10; //  ЗАКАЗ ВЫПОЛНЕН (заказ собран и отгружен со склада, данные отправлены по API клиенты (если это необходимо) )
    const CANCEL = 11;   // ОТМЕНЕН
    const PRINT_PICKING_LIST = 12;   // Напечатали лист сборки
    const PRINT_BOX_LABEL = 13;   // Напечатали этикетки на коробки

    public static function getOrdersForPrintPickList() {
        return [
            self::_NEW,
            self::FULL_RESERVED,
            self::PART_RESERVED,
            self::PRINTED_PICKING_LIST,
        ];
    }

    public static function getNotDoneOrders() {
        return [
            self::_NEW,
            self::FULL_RESERVED,
            self::PART_RESERVED,
            self::PRINTED_PICKING_LIST,
            self::SCANNING,
            self::SCANNED,
        ];
    }

    /**
     * @param string $lang
     * @return array Массив с статусами.
     */
    public static function getAll($lang = null)
    {
        return [
            self::CANCEL => \Yii::t('stock/titles', 'Cancel',[],$lang),
            self::_NEW => \Yii::t('stock/titles', 'New',[],$lang),
            self::RESERVING => \Yii::t('stock/titles', 'Process reserving',[],$lang),
            self::FULL_RESERVED => \Yii::t('stock/titles', 'Full reserved',[],$lang),//разные
            self::PART_RESERVED => \Yii::t('stock/titles', 'Part reserved',[],$lang),//разные
            self::PRINT_PICKING_LIST => \Yii::t('stock/titles', 'Print picking list',[],$lang),
            self::ON_ROAD => \Yii::t('stock/titles', 'On road',[],$lang),
            self::DELIVERED => \Yii::t('stock/titles', 'Delivered',[],$lang),
            self::SCANNING => \Yii::t('stock/titles', 'Scanning',[],$lang),//один
            self::SCANNED => \Yii::t('stock/titles', 'Scanned',[],$lang),//один
            self::DONE => \Yii::t('stock/titles', 'Given to courier',[],$lang),
            self::PRINT_BOX_LABEL => \Yii::t('stock/titles', 'Print box label',[],$lang),
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

    /*
  * Высчитывает разницу между датами в рабочих днях
  * @param mixed $start дата нaчала
  * @param mixed $end дата конца
  * @return int
  **/
    public static function getStockGridColor($status)
    {

        switch($status) {
            case self::_NEW: //#FFA54F
                $class = 'color-tan';
                break;
            case self::FULL_RESERVED: //#FFA500
                $class = 'color-orange';
                break;
            case self::RESERVING: //#FFF68F
                $class = 'color-khaki';
                break;
            case self::PART_RESERVED: //#CAFF70
                $class = 'color-dark-olive-green';
                break;
            case self::PRINT_PICKING_LIST: //#87CEFA
                $class = 'color-light-sky-blue';
                break;
            case self::SCANNING: //#1E90FF
                $class = 'color-dodger-blue';
                break;
            case self::SCANNED: //#FFFFE0
                $class = 'color-light-yellow';
                break;
            case self::PRINT_BOX_LABEL: //#EE82EE
                $class = 'color-violet ';
                break;
            case self::DELIVERED: //#FF6A6A
            case self::ON_ROAD: //#FF6A6A
                $class = 'color-indian-red';
                break;
            case self::CANCEL: //#C6E2FF
                $class = 'color-slate-gray';
                break;
            default:
                $class = '';
                break;

        }
        return $class;
    }

}