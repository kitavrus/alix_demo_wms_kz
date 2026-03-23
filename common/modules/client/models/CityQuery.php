<?php

namespace common\modules\client\models;

/**
 * This is the ActiveQuery class for [[ClientGroup]].
 *
 * @see ClientGroup
 */
class CityQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ClientGroup[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ClientGroup|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
