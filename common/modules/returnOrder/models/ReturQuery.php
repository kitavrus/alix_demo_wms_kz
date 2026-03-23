<?php

namespace common\modules\returnOrder\models;

/**
 * This is the ActiveQuery class for [[ReturnTmpOrders]].
 *
 * @see ReturnTmpOrders
 */
class ReturQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ReturnTmpOrders[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ReturnTmpOrders|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
