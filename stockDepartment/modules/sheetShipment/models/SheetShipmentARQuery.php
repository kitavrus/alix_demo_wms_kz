<?php

namespace stockDepartment\modules\sheetShipment\models;

/**
 * This is the ActiveQuery class for [[SheetShipmentAR]].
 *
 * @see SheetShipmentAR
 */
class SheetShipmentARQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return SheetShipmentAR[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return SheetShipmentAR|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
