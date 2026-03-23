<?php

namespace stockDepartment\modules\alix\controllers\api\v1\outbound\constants;

use yii\helpers\ArrayHelper;

class OutboundStatus
{
    /*
 * @var status
 * */
    const NOT_SET = 0;// Статус не определен

    const _NEW = 1;
    const SCANNING = 2; //  СКАНИРУЕТСЯ
    const SCANNED = 3;  //  ОТСКАНИРОВАН
    const PLACED = 4;  //  РАЗМЕЩЕН
    const DONE = 5;    // ОТПРАВЛЕН ПО АПИ
    const CANCEL = 6;   // ОТМЕНЕН
	const COMPLETE = 7;   // РЕБЯТА ЗАВЕРШИЛИ СКАНИРОВАНИЕ

    public static function getNewAndInProcessOrder() {
        return [
            self:: _NEW,
            self::SCANNING,
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


    /**
     * @param string $lang
     * @return array Массив с статусами.
     */
    public static function getAll($lang = null)
    {
        return [
            self::CANCEL => \Yii::t('stock/titles', 'Cancel',[],$lang),
            self::_NEW => \Yii::t('stock/titles', 'New',[],$lang),
            self::SCANNING => \Yii::t('stock/titles', 'Scanning',[],$lang),//один
            self::SCANNED => \Yii::t('stock/titles', 'Scanned',[],$lang),//один
            self::DONE => \Yii::t('stock/titles', 'Done',[],$lang),
			self::COMPLETE => \Yii::t('stock/titles', 'Complete',[],$lang),
        ];
    }
}