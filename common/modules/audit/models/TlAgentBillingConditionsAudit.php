<?php
/**
 * Created by PhpStorm.
 * Date: 08.01.2015
 * Time: 12:14
 */
namespace common\modules\audit\models;

use common\modules\agentBilling\models\TlAgentBillingConditions;
use common\modules\audit\interfaces\AuditInterface;

class TlAgentBillingConditionsAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_agents_billing_conditions_audit';
    }

    /**
     * @inheritdoc
     */
    public function getAuditObjectClass()
    {
        return TlAgentBillingConditions::className();
    }

}