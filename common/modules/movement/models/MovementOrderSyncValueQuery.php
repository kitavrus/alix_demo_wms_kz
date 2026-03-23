<?php

namespace common\modules\movement\models;

/**
 * This is the ActiveQuery class for [[MovementOrderSyncValue]].
 *
 * @see MovementOrderSyncValue
 */
class MovementOrderSyncValueQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MovementOrderSyncValue[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MovementOrderSyncValue|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}