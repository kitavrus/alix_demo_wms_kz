<?php

namespace common\modules\audit\models;

use common\modules\transportLogistics\models\TlDeliveryProposalRouteTransport;
use common\modules\audit\interfaces\AuditInterface;

class TlDeliveryProposalRouteTransportAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposal_routes_car_audit';
    }

    /**
     * @inheritdoc
     */
    public function getAuditObjectClass()
    {
        return TlDeliveryProposalRouteTransport::className();
    }
}
