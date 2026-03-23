<?php

use yii\db\Migration;

/**
 * Class m200420_070843_ecommerce_add_transfer_id_to_stock
 */
class m200420_070843_ecommerce_add_transfer_id_to_stock extends Migration
{

    public function up()
    {
        $this->addColumn('{{%ecommerce_stock}}', 'transfer_id', $this->integer()->defaultValue(0)->comment("Transfer id")->after('outbound_item_id'));
        $this->addColumn('{{%ecommerce_stock}}', 'transfer_item_id', $this->integer()->defaultValue(0)->comment("Transfer item id")->after('transfer_id'));
        $this->addColumn('{{%ecommerce_stock}}', 'status_transfer', $this->integer()->defaultValue(0)->comment("Transfer status")->after('transfer_item_id'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_stock}}', 'transfer_id');
        $this->dropColumn('{{%ecommerce_stock}}', 'transfer_item_id');
        $this->dropColumn('{{%ecommerce_stock}}', 'status_transfer');
        return false;
    }
}