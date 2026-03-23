<?php

use yii\db\Schema;
use yii\db\Migration;

class m160215_072755_add_field_unique_key_table_bookkeeper extends Migration
{
    public function up()
    {
        $this->addColumn('{{%bookkeeper}}', 'unique_key', Schema::TYPE_STRING. '(64) NULL DEFAULT "" comment "" AFTER  `tl_delivery_proposal_route_unforeseen_expenses_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%bookkeeper}}', 'unique_key');
    }
}