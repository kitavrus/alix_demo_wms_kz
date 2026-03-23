<?php

use yii\db\Migration;

/**
 * Class m200520_124341_ecommerce_transfer_outbound_box_to_stock
 */
class m200520_124341_ecommerce_transfer_outbound_box_to_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%ecommerce_stock}}', 'transfer_outbound_box', $this->string(16)->defaultValue('')->comment("Transfer outbound box")->after('transfer_box_check_step'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_stock}}', 'transfer_outbound_box');
        return false;
    }
}
