<?php

namespace common\modules\audit\models;

use common\modules\transportLogistics\models\TlOutboundRegistry;
use common\modules\audit\interfaces\AuditInterface;
class TlOutboundRegistryAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_outbound_registry_audit';
    }

    public function getAuditObjectClass()
    {
        return TlOutboundRegistry::className();
    }

}
