<?php

use yii\db\Schema;
use yii\db\Migration;

class m141128_114604_add_field_extra_fields_to_table_tl_delivery_proposals extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','extra_fields',Schema::TYPE_TEXT . ' DEFAULT "" COMMENT "Example JSON: order_number,
who received order, etc ... " AFTER `comment`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposals}}','extra_fields');
    }
}
