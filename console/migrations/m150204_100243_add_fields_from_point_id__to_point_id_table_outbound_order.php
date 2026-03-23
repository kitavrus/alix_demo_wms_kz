<?php

use yii\db\Schema;
use yii\db\Migration;

class m150204_100243_add_fields_from_point_id__to_point_id_table_outbound_order extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_orders}}','from_point_id',Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT  "Internal from point id" AFTER `warehouse_id`');
        $this->addColumn('{{%outbound_orders}}','to_point_id',Schema::TYPE_INTEGER . ' NULL DEFAULT "0" COMMENT  "Internal to point id" AFTER `from_point_id`');

        $this->addColumn('{{%outbound_orders}}','to_point_title',Schema::TYPE_STRING . '(256) NULL DEFAULT "" COMMENT  "Internal from point text value" AFTER `to_point_id`');
        $this->addColumn('{{%outbound_orders}}','from_point_title',Schema::TYPE_STRING . '(256) NULL DEFAULT "" COMMENT  "Internal from point text value" AFTER `to_point_title`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_orders}}','from_point_id');
        $this->dropColumn('{{%outbound_orders}}','to_point_id');

        $this->dropColumn('{{%outbound_orders}}','to_point_title');
        $this->dropColumn('{{%outbound_orders}}','from_point_title');

        return false;
    }
}
