<?php

use yii\db\Schema;
use yii\db\Migration;

class m160120_043732_add_field_type_id_table_tl_agents extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_agents}}', 'payment_period', Schema::TYPE_SMALLINT. ' NULL DEFAULT "1" comment "" AFTER  `status`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_agents}}', 'payment_period');
    }
}
