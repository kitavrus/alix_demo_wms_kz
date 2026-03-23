<?php

use yii\db\Migration;

class m170830_180905_add_fields_pick_list_status_table_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stock}}','pick_list_status',$this->smallInteger()->defaultValue(1)->comment("Pick list scan status")->after('status'));
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}','pick_list_status');
        return false;
    }
}