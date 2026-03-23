<?php

use yii\db\Migration;

class m170928_094307_add_field_Nop__Size_table_products extends Migration
{
    public function init()
    {
        $this->db = 'dbDefactoSpecial';
        parent::init();
    }

    public function up()
    {
        $this->addColumn('{{%products}}','Nop',$this->integer()->defaultValue(0)->comment("Qty items in Product")->after('Color'));
        $this->addColumn('{{%products}}','Size',$this->string()->defaultValue('')->comment("Size product in lot")->after('Nop'));
    }

    public function down()
    {
        $this->dropColumn('{{%products}}','Nop');
        $this->dropColumn('{{%products}}','Size');
        return false;
    }
}