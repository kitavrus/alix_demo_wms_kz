<?php

namespace common\modules\movement\models;

/**
 * This is the ActiveQuery class for [[MovementItems]].
 *
 * @see MovementItems
 */
class MovementItemsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MovementItems[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MovementItems|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
