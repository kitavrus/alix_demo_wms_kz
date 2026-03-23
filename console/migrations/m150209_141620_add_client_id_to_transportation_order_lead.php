<?php

use yii\db\Schema;
use yii\db\Migration;

class m150209_141620_add_client_id_to_transportation_order_lead extends Migration
{
    public function up()
    {
        $this->addColumn('{{%transportation_order_lead}}','client_id',Schema::TYPE_INTEGER . ' NULL AFTER `id`');
    }

    public function down()
    {
        $this->dropColumn('{{%transportation_order_lead}}','client_id');
    }
}
