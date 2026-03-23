<?php
/**
 * Created by PhpStorm.
 * Date: 08.01.2015
 * Time: 12:14
 */
namespace common\modules\audit\models;

use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\audit\interfaces\AuditInterface;

class TlDeliveryProposalBillingAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposal_billing_audit';
    }

    /**
     * @inheritdoc
     */
    public function getAuditObjectClass()
    {
        return TlDeliveryProposalBilling::className();
    }
}