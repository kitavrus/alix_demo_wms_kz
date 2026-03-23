<?php

namespace common\modules\transportLogistics\models;

/**
 * This is the ActiveQuery class for [[TlDeliveryProposalRouteUnforeseenExpensesType]].
 *
 * @see TlDeliveryProposalRouteUnforeseenExpensesType
 */
class TlDeliveryProposalQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        $this->andWhere('[[status]] = :status',[':status'=>TlDeliveryProposalRouteUnforeseenExpensesType::STATUS_ACTIVE]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return TlDeliveryProposalRouteUnforeseenExpensesType[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TlDeliveryProposalRouteUnforeseenExpensesType|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}