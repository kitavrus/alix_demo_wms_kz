<?php

use yii\db\Migration;

/**
 * Class m200513_140632_ecommerce_transfer_box_check_step_to_stock
 */
class m200513_140632_ecommerce_transfer_box_check_step_to_stock extends Migration
{

    public function up()
    {
        $this->addColumn('{{%ecommerce_stock}}', 'transfer_box_check_step', $this->string(16)->defaultValue('')->comment("Transfer box check step")->after('status_transfer'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_stock}}', 'transfer_box_check_step');
        return false;
    }
}
