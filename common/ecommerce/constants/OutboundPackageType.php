<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 24.07.2019
 * Time: 19:59
 */

namespace common\ecommerce\constants;


use yii\helpers\ArrayHelper;

class OutboundPackageType
{
    const NOT_SET = 0;// Тип не определен

    const KOLI1 = 4;
    const KOLI2 = 5;
    const KOLI4 = 7;
    const PAKET1 = 8;
    const PAKET3 = 10;
    const PAKET5 = 12;
    const MNG3 = 15;
    const KOLI5 = 16;
    const KOLI6 = 17;
    const PAKET2 = 18;
    const MNG2 = 19;
    const PAKET4 = 20;
    const MNG1 = 21;
    const KOLI3 = 22;
    const KOLI7 = 23;
    const CARGO_PAKET1 = 24;
    const CARGO_PAKET2 = 25;
    const CARGO_PAKET3 = 26;

    /**
     * @return array Массив с статусами.
     */
    public static function getAll()
    {
        return [
            self::KOLI1 => \Yii::t('stock/titles', 'KOLI1'),
            self::KOLI2 => \Yii::t('stock/titles', 'KOLI2'),
            self::KOLI4 => \Yii::t('stock/titles', 'KOLI4'),
            self::PAKET1 => \Yii::t('stock/titles', 'PAKET1'),
            self::PAKET3 => \Yii::t('stock/titles', 'PAKET3'),
            self::PAKET5 => \Yii::t('stock/titles', 'PAKET5'),
            self::MNG3 => \Yii::t('stock/titles', 'MNG3'),
            self::KOLI5 => \Yii::t('stock/titles', 'KOLI5'),
            self::KOLI6 => \Yii::t('stock/titles', 'KOLI6'),
            self::PAKET2 => \Yii::t('stock/titles', 'PAKET2'),
            self::MNG2 => \Yii::t('stock/titles', 'MNG2'),
            self::PAKET4 => \Yii::t('stock/titles', 'PAKET4'),
            self::MNG1 => \Yii::t('stock/titles', 'MNG1'),
            self::KOLI3 => \Yii::t('stock/titles', 'KOLI3'),
            self::KOLI7 => \Yii::t('stock/titles', 'KOLI7'),
            self::CARGO_PAKET1 => \Yii::t('stock/titles', 'CARGO_PAKET1'),
            self::CARGO_PAKET2 => \Yii::t('stock/titles', 'CARGO_PAKET2'),
            self::CARGO_PAKET3 => \Yii::t('stock/titles', 'CARGO_PAKET3'),
        ];
    }

    /**
     * @return string Читабельный статус поста.
     */
    public static function getValue($value = null)
    {
        return ArrayHelper::getValue(self::getAll(), $value);
    }

    public static function isExist($value)
    {
        return array_key_exists($value,self::getAll());
    }

}