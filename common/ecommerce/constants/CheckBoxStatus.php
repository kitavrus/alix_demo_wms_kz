<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 24.07.2019
 * Time: 19:59
 */
namespace common\ecommerce\constants;

use yii\helpers\ArrayHelper;

class CheckBoxStatus
{
    /*
     * @var status
     * */
    const NEW_ = 0;
    const START_SCANNED = 1;
    const END_SCANNED = 2;
    const END = 3;
    const DONE = 4;
    const NO_SCANNING = 5;
    const STOCK_ADJUSTMENT = 6;

    /**
     * @param string $lang
     * @return array Массив с статусами.
     */
    public static function getAll($lang = null)
    {
        return [
            self::NEW_ => \Yii::t('stock/titles', 'NEW',[],$lang),
            self::START_SCANNED => \Yii::t('stock/titles', 'START_SCANNED',[],$lang),
            self::END_SCANNED => \Yii::t('stock/titles', 'END_SCANNED',[],$lang),
            self::END => \Yii::t('stock/titles', 'END',[],$lang),
            self::DONE => \Yii::t('stock/titles', 'DONE',[],$lang),
            self::STOCK_ADJUSTMENT => \Yii::t('stock/titles', 'STOCK_ADJUSTMENT',[],$lang),
//            self::NO_SCANNING => \Yii::t('stock/titles', 'NO SCANNING',[],$lang),
        ];
    }

    public static function getNewAndInProcessOrder() {
        return [
            self:: NEW_,
            self::START_SCANNED,
        ];
    }

    public static function isDone($status) {
        return  self::DONE == $status;
    }


    /**
     * @param int $status
     * @param string $lang
     * @return string Читабельный статус поста.
     */
    public static function getValue($status = null,$lang = null)
    {
        if($status == self::NO_SCANNING) {
            return \Yii::t('stock/titles', 'NO SCANNING',[],$lang);
        }

        return ArrayHelper::getValue(self::getAll($lang), $status);
    }


    /**
     * @param int $status
     * @param string $lang
     * @return string Читабельный статус поста.
     */
    public static function getCssClass($status)
    {
        switch($status) {
            case self::NEW_;
                $class = 'alert-danger';
                break;
            case self::END_SCANNED:
                $class = 'alert-success';
                break;
            case self::NO_SCANNING:
                $class = 'alert-warning';
                break;
            case self::STOCK_ADJUSTMENT:
                $class = 'alert-info';
                break;
            default:
                $class = '';
                break;
        }
        return $class;
    }
}