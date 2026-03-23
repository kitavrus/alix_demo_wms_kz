<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08.01.2015
 * Time: 12:14
 */
namespace common\modules\audit\models;
use common\modules\inbound\models\InboundOrder;
use common\modules\audit\models\Audit;
use common\modules\audit\interfaces\AuditInterface;

class InboundOrderAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inbound_orders_audit';
    }

    /**
     * @inheritdoc
     */
    public function getAuditObjectClass()
    {
        return InboundOrder::className();
    }

}