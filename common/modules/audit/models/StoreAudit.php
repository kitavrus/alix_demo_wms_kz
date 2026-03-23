<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08.01.2015
 * Time: 12:14
 */
namespace common\modules\audit\models;

use common\modules\store\models\Store;
use common\modules\audit\interfaces\AuditInterface;

class StoreAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'store_audit';
    }

    /**
     * @inheritdoc
     */
    public function getAuditObjectClass()
    {
        return Store::className();
    }

}