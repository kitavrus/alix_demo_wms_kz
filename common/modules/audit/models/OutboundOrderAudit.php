<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08.01.2015
 * Time: 12:14
 */
namespace common\modules\audit\models;
use common\modules\outbound\models\OutboundOrder;
use common\modules\audit\interfaces\AuditInterface;
class OutboundOrderAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outbound_orders_audit';
    }

    /**
     * @inheritdoc
     */
    public function getAuditObjectClass()
    {
        return OutboundOrder::className();
    }

}