<?php

namespace common\modules\stock\models;

/**
 * This is the ActiveQuery class for [[ConsignmentUniversalOrdersItems]].
 *
 * @see ConsignmentUniversalOrdersItems
 */
class ConsignmentUniversalOrdersItemsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ConsignmentUniversalOrdersItems[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ConsignmentUniversalOrdersItems|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}