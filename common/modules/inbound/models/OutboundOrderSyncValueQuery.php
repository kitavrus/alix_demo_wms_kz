<?php

namespace common\modules\inbound\models;

/**
 * This is the ActiveQuery class for [[OutboundOrderSyncValue]].
 *
 * @see OutboundOrderSyncValue
 */
class OutboundOrderSyncValueQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return OutboundOrderSyncValue[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OutboundOrderSyncValue|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}