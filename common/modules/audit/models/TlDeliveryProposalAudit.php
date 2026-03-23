<?php

namespace common\modules\audit\models;

use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\audit\interfaces\AuditInterface;
class TlDeliveryProposalAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposals_audit';
    }

    /**
     * @inheritdoc
     */
    public function getAuditObjectClass()
    {
        return TlDeliveryProposal::className();
    }
}
