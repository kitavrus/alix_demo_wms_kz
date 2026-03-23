<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08.01.2015
 * Time: 12:14
 */
namespace common\modules\audit\models;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\audit\interfaces\AuditInterface;

class OutboundPickingListsAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outbound_picking_lists_audit';
    }

    /**
     * @inheritdoc
     */
    public function getAuditObjectClass()
    {
        return OutboundPickingLists::className();
    }

}