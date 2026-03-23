<?php

namespace common\modules\movement\models;

/**
 * This is the ActiveQuery class for [[Movement]].
 *
 * @see Movement
 */
class MovementQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Movement[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Movement|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
