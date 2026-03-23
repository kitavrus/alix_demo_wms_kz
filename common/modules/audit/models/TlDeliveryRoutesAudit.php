<?php

namespace common\modules\audit\models;

use common\modules\transportLogistics\models\TlDeliveryRoutes;
use common\modules\audit\interfaces\AuditInterface;
class TlDeliveryRoutesAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposal_routes_audit';
    }

    public function getAuditObjectClass()
    {
        return TlDeliveryRoutes::className();
    }

}
