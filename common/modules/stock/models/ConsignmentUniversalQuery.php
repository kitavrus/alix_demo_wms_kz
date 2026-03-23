<?php

namespace common\modules\stock\models;

/**
 * This is the ActiveQuery class for [[ConsignmentUniversal]].
 *
 * @see ConsignmentUniversal
 */
class ConsignmentUniversalQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ConsignmentUniversal[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ConsignmentUniversal|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
