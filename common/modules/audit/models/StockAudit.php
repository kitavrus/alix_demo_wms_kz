<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08.01.2015
 * Time: 12:14
 */
namespace common\modules\audit\models;
use common\modules\stock\models\Stock;
use common\modules\audit\interfaces\AuditInterface;

class StockAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stock_audit';
    }

    /**
     * @inheritdoc
     */
    public function getAuditObjectClass()
    {
        return Stock::className();
    }

}