<?php
namespace app\modules\intermode\controllers\ecommerce\stock\domain\constants;


use yii\helpers\ArrayHelper;

class StockConditionType
{
    const NOT_SET = 0; //не определен
    const UNDAMAGED = 1; //Неповрежденный
    const PARTIAL_DAMAGED = 2; //частично поврежден
    const FULL_DAMAGED = 3; //полностью поврежден

    /*
   * Availability status array
   * return mixed
   **/
    public function getConditionTypeArray()
    {
        $data = [
//            self::NOT_SET => Yii::t('stock/titles', 'Not set'),
            self::UNDAMAGED => \Yii::t('stock/titles', 'Неповрежденный'),
//            self::PARTIAL_DAMAGED => \Yii::t('stock/titles', 'Частично поврежден'),
            self::FULL_DAMAGED => \Yii::t('stock/titles', 'Полность поврежден'),
        ];

        return $data;
    }

    /**
     * @return string Читабельный статус.
     */
    public function getConditionTypeValue($condition_type=null)
    {
        return ArrayHelper::getValue($this->getConditionTypeArray(), $condition_type);
    }
}