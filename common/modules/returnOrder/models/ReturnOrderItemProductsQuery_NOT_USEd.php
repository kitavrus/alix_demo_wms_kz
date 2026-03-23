<?php

namespace common\modules\returnOrder\models;

/**
 * This is the ActiveQuery class for [[ReturnOrderItemProduct]].
 *
 * @see ReturnOrderItemProduct
 */
class ReturnOrderItemProductsQuery extends \common\models\ActiveRecord
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ReturnOrderItemProduct[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ReturnOrderItemProduct|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}