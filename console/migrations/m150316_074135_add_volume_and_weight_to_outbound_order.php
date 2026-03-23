<?php

use yii\db\Schema;
use yii\db\Migration;

class m150316_074135_add_volume_and_weight_to_outbound_order extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_orders}}','mc', Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Volume" AFTER `status`');
        $this->addColumn('{{%outbound_orders}}','kg', Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Weight" AFTER `mc`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_orders}}','mc');
        $this->dropColumn('{{%outbound_orders}}','kg');
    }
}
