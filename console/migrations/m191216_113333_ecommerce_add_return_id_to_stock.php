<?php

use yii\db\Migration;

/**
 * Class m191216_113333_ecommerce_add_return_id_to_stock
 */
class m191216_113333_ecommerce_add_return_id_to_stock extends Migration
{

    public function up()
    {
        $this->addColumn('{{%ecommerce_stock}}', 'return_id', $this->integer()->defaultValue(0)->comment("Return id")->after('outbound_id'));
        $this->addColumn('{{%ecommerce_stock}}', 'return_item_id', $this->integer()->defaultValue(0)->comment("Return item id")->after('return_id'));
        $this->addColumn('{{%ecommerce_stock}}', 'status_return', $this->integer()->defaultValue(0)->comment("Return status")->after('status_outbound'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_stock}}', 'return_id');
        $this->dropColumn('{{%ecommerce_stock}}', 'return_item_id');
        $this->dropColumn('{{%ecommerce_stock}}', 'status_return');
        return false;
    }
}
