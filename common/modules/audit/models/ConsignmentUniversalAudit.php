<?php
/**
 * Created by PhpStorm.
 * Date: 08.01.2015
 * Time: 12:14
 */
namespace common\modules\audit\models;

use common\modules\crossDock\models\CrossDock;
use common\modules\audit\interfaces\AuditInterface;
use common\modules\stock\models\ConsignmentUniversal;

class ConsignmentUniversalAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'consignment_universal_audit';
    }

    /**
     * @inheritdoc
     */
    public function getAuditObjectClass()
    {
        return ConsignmentUniversal::className();
    }
}