<?php

use yii\db\Schema;
use yii\db\Migration;

class m150622_173843_alter_table_proposal_leads extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%transportation_order_lead}}','weight',Schema::TYPE_DECIMAL . '(26,3) NULL');
        $this->alterColumn('{{%transportation_order_lead}}','volume',Schema::TYPE_DECIMAL . '(26,3) NULL');
    }

    public function down()
    {


        return false;
    }
}
