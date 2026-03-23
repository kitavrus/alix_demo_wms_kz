<?php

use yii\db\Migration;

/**
 * Class m200511_055747_ecommerce_transfer_add_box_qty
 */
class m200511_055747_ecommerce_transfer_add_box_qty extends Migration
{

    public function up()
    {
        $this->addColumn('{{%ecommerce_transfer}}', 'expected_box_qty', $this->integer()->defaultValue(0)->comment("Expected box qty")->after('client_LcBarcode'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_transfer}}', 'expected_box_qty');
        return false;
    }
}
