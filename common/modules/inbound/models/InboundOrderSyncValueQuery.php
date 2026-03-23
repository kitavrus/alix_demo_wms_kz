<?php

namespace common\modules\inbound\models;

/**
 * This is the ActiveQuery class for [[InboundOrderSyncValue]].
 *
 * @see InboundOrderSyncValue
 */
class InboundOrderSyncValueQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return InboundOrderSyncValue[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return InboundOrderSyncValue|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
