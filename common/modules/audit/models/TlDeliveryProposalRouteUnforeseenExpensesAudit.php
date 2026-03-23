<?php

namespace common\modules\audit\models;

use common\modules\transportLogistics\models\TlDeliveryProposalRouteUnforeseenExpenses;
use common\modules\audit\interfaces\AuditInterface;

class TlDeliveryProposalRouteUnforeseenExpensesAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposal_route_unforeseen_expenses_audit';
    }

    /**
     * @inheritdoc
     */
    public function getAuditObjectClass()
    {
        return TlDeliveryProposalRouteUnforeseenExpenses::className();
    }
}
