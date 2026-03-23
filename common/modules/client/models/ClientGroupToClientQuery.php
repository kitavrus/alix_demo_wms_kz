<?php

namespace common\modules\client\models;

/**
 * This is the ActiveQuery class for [[ClientGroupToClient]].
 *
 * @see ClientGroupToClient
 */
class ClientGroupToClientQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ClientGroupToClient[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ClientGroupToClient|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}