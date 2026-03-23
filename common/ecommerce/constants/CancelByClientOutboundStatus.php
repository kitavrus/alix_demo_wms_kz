<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 24.07.2019
 * Time: 19:59
 */

namespace common\ecommerce\constants;


use yii\helpers\ArrayHelper;

class CancelByClientOutboundStatus
{
    /*
     * @var status
     * */
    const NOT_SET = 0;// Статус не определен
    const _NEW = 1; //- НОВЫЙ
    const SCANNING = 2; //
    const DONE = 3; //

    /**
     * @return array Массив с статусами.
     */
    public static function getAll()
    {
        return [
            self::NOT_SET => 'NOT_SET', //\Yii::t('stock/titles', 'Не указано'),
            self::_NEW => 'NEW', //\Yii::t('stock/titles', 'NEW'),
            self::SCANNING => 'SCANNING', //\Yii::t('stock/titles', 'SCANNING'),
            self::DONE => 'DONE', //\Yii::t('stock/titles', 'DONE'),
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