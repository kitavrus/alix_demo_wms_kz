<?php

namespace common\modules\audit\models;

use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;
use common\modules\audit\interfaces\AuditInterface;
class TlDeliveryProposalRouteCarsAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposal_route_cars_audit';
    }

    /**
     * @inheritdoc
     */
    public function getAuditObjectClass()
    {
        return TlDeliveryProposalRouteCars::className();
    }
}
