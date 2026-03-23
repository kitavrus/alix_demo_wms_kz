<?php

use yii\db\Schema;
use yii\db\Migration;

class m150408_110848_change_column_party_number_table_consignment_inbound_orders extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%consignment_inbound_orders}}','party_number',Schema::TYPE_STRING . '(128) NULL DEFAULT ""');
    }

    public function down()
    {
        $this->alterColumn('{{%consignment_inbound_orders}}','party_number',Schema::TYPE_INTEGER . '(11)');

        return false;
    }
}
