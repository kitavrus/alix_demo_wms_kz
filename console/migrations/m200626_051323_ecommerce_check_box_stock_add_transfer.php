<?php

use yii\db\Migration;

/**
 * Class m200626_051323_ecommerce_check_box_stock_add_transfer
 */
class m200626_051323_ecommerce_check_box_stock_add_transfer extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%ecommerce_check_box_stock}}', 'stock_transfer_id', $this->string(18)->defaultValue('')->comment("")->after('stock_condition_type'));
        $this->addColumn('{{%ecommerce_check_box_stock}}', 'stock_status_transfer', $this->string(18)->defaultValue('')->comment("")->after('stock_transfer_id'));
        $this->addColumn('{{%ecommerce_check_box_stock}}', 'stock_transfer_outbound_box', $this->string(18)->defaultValue('')->comment("")->after('stock_status_transfer'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%ecommerce_check_box_stock}}', 'stock_transfer_id');
        $this->dropColumn('{{%ecommerce_check_box_stock}}', 'stock_status_transfer');
        $this->dropColumn('{{%ecommerce_check_box_stock}}', 'stock_transfer_outbound_box');
        return false;
    }
}