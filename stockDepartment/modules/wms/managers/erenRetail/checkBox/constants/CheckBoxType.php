<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 24.07.2019
 * Time: 19:59
 */
namespace stockDepartment\modules\wms\managers\erenRetail\checkBox\constants;

use yii\helpers\ArrayHelper;

class CheckBoxType
{
    /*
     * @var status
     * */
    const PART_BY_BOX = 0;
    const FULL = 1;
    /**
     * @param string $lang
     * @return array Массив с статусами.
     */
    public static function getAll($lang = null)
    {
        return [
            self::PART_BY_BOX => \Yii::t('stock/titles', 'PART_BY_BOX',[],$lang),
            self::FULL => \Yii::t('stock/titles', 'FULL',[],$lang),
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