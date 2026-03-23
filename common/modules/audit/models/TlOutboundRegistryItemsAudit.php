<?php

namespace common\modules\audit\models;

use common\modules\transportLogistics\models\TlOutboundRegistryItems;
use common\modules\audit\interfaces\AuditInterface;
class TlOutboundRegistryItemsAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_outbound_registry_items_audit';
    }

    public function getAuditObjectClass()
    {
        return TlOutboundRegistryItems::className();
    }

}
