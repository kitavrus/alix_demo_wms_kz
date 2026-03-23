<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08.01.2015
 * Time: 12:14
 */
namespace common\modules\audit\models;
use common\modules\transportLogistics\models\TlAgents;
use common\modules\audit\interfaces\AuditInterface;

class TlAgentsAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_agents_audit';
    }

    /**
     * @inheritdoc
     */
    public function getAuditObjectClass()
    {
        return TlAgents::className();
    }

}