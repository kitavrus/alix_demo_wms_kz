<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08.01.2015
 * Time: 12:14
 */
namespace common\modules\audit\models;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\audit\interfaces\AuditInterface;
class InboundOrderItemAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inbound_order_items_audit';
    }

    /**
     * @inheritdoc
     */
    public function getAuditObjectClass()
    {
        return InboundOrderItem::className();
    }

}