<?php

use yii\db\Schema;
use yii\db\Migration;

class m141211_102045_add_field_bl_rules_table_tl_delivery_proposal extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','bl_data',Schema::TYPE_TEXT . ' DEFAULT "" COMMENT "Example: last change data,
datetime set status on route, etc ... " AFTER `comment`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposals}}','bl_data');
    }
}
