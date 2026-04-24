<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\constants;

use yii\helpers\ArrayHelper;

class InboundStatus
{
    const NOT_SET = 0;
    const _NEW = 1;
    const SCANNING = 2;
    const SCANNED = 3;
    const PLACED = 4;
    const DONE = 5;
    const CANCEL = 6;
    const COMPLETE = 7;

    public static function getNewAndInProcessOrder()
    {
        return [
            self::_NEW,
            self::SCANNING,
        ];
    }

    public static function getValue($status = null, $lang = null)
    {
        return ArrayHelper::getValue(self::getAll($lang), $status);
    }

    public static function getAll($lang = null)
    {
        return [
            self::CANCEL   => \Yii::t('stock/titles', 'Cancel', [], $lang),
            self::_NEW     => \Yii::t('stock/titles', 'New', [], $lang),
            self::SCANNING => \Yii::t('stock/titles', 'Scanning', [], $lang),
            self::SCANNED  => \Yii::t('stock/titles', 'Scanned', [], $lang),
            self::DONE     => \Yii::t('stock/titles', 'Done', [], $lang),
            self::COMPLETE => \Yii::t('stock/titles', 'Complete', [], $lang),
        ];
    }
}
