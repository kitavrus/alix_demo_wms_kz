<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 24.07.2019
 * Time: 19:59
 */

namespace stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\constants;


use yii\helpers\ArrayHelper;

class CourierCompany
{
    /*
 * @var status
 * */
    const NOT_SET = 0;// Статус не определен

    const PONY_EXPRESS = 'Pony';
    const PONY_EXPRESS_KASPI = 'KASPI';
    const DPD = 'DPD';
    const DHL = 'DHL';
    const GALLOP = 'GALLOP';
    const EXLINE = 'EXLINE';
    const PARTNER = 'PARTNER';
    const KAZPOST = 'KazPost';
    const LAMODA = 'LAMODA';

    /**
     * @param string $lang
     * @return array Массив с статусами.
     */
    public static function getAll($lang = null)
    {
        return [
            self::PONY_EXPRESS_KASPI => \Yii::t('stock/titles', 'Kaspi',[],$lang),
           // self::PONY_EXPRESS => \Yii::t('stock/titles', 'Pony express',[],$lang),
           // self::KAZPOST => \Yii::t('stock/titles', 'Казпочта',[],$lang),
           // self::GALLOP => \Yii::t('stock/titles', 'Gallop',[],$lang),
            self::LAMODA => \Yii::t('stock/titles', 'Lamoda',[],$lang),
           // self::DPD => \Yii::t('stock/titles', 'dpd',[],$lang),
           // self::DHL => \Yii::t('stock/titles', 'DHL',[],$lang),
           // self::EXLINE => \Yii::t('stock/titles', 'Exline',[],$lang),
           // self::PARTNER => \Yii::t('stock/titles', 'Partner',[],$lang),
        ];
    }

    /**
     * @param int $status
     * @param string $lang
     * @return string Читабельный статус поста.
     */
    public static function getValue($status = null,$lang = null)
    {
        //if($status == self::PONY_EXPRESS_KASPI) {
        //    $status = self::PONY_EXPRESS;
       // }

        return ArrayHelper::getValue(self::getAll($lang), $status);
    }
}