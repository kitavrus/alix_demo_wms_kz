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
    const NO = 0;
    const YES = 1;
    /**
     * @param string $lang
     * @return array Массив с статусами.
     */
    public static function getAll($lang = null)
    {
        return [
            self::NO => \Yii::t('stock/titles', 'NO',[],$lang),
            self::YES => \Yii::t('stock/titles', 'Yes',[],$lang),
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