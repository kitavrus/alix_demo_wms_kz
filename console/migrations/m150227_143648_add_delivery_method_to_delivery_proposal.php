<?php

use yii\db\Schema;
use yii\db\Migration;

class m150227_143648_add_delivery_method_to_delivery_proposal extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','delivery_method', Schema::TYPE_INTEGER . ' NULL AFTER `delivery_type`');
    }

    public function down()
    {
        echo "m150227_143648_add_delivery_method_to_delivery_proposal cannot be reverted.\n";

        return false;
    }
}
