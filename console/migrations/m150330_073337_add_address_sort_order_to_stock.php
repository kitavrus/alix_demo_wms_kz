<?php

use yii\db\Schema;
use yii\db\Migration;

class m150330_073337_add_address_sort_order_to_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','address_sort_order',Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "Address sort order" AFTER `secondary_address`');
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','address_sort_order');
    }
}
