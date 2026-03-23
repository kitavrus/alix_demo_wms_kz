<?php

use yii\db\Schema;
use yii\db\Migration;

class m150730_064227_add_title_description_to_outbound_order extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_orders}}','title',Schema::TYPE_STRING . ' NULL DEFAULT NULL  AFTER `extra_fields`');
        $this->addColumn('{{%outbound_orders}}','description',Schema::TYPE_TEXT . ' NULL DEFAULT NULL AFTER `title`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_orders}}','title');
        $this->dropColumn('{{%outbound_orders}}','description');
    }

}