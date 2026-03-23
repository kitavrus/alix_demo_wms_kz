<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08.01.2015
 * Time: 12:14
 */
namespace common\modules\audit\models;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\audit\interfaces\AuditInterface;

class OutboundOrderItemAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outbound_order_items_audit';
    }

    /**
     * @inheritdoc
     */
    public function getAuditObjectClass()
    {
        return OutboundOrderItem::className();
    }

}