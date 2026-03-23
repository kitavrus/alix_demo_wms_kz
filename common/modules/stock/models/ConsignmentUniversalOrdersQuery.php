<?php

namespace common\modules\stock\models;

/**
 * This is the ActiveQuery class for [[ConsignmentUniversalOrders]].
 *
 * @see ConsignmentUniversalOrders
 */
class ConsignmentUniversalOrdersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ConsignmentUniversalOrders[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ConsignmentUniversalOrders|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
