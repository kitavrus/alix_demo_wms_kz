<?php
namespace stockDepartment\modules\alix\controllers\inbound\domain;

/**
 * This is the ActiveQuery class for [[InboundDataMatrix]].
 *
 * @see InboundDataMatrix
 */
class InboundDataMatrixQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return InboundDataMatrix[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return InboundDataMatrix|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
