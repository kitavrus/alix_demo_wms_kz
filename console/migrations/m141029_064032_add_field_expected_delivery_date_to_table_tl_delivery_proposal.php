<?php

use yii\db\Schema;
use yii\db\Migration;

class m141029_064032_add_field_expected_delivery_date_to_table_tl_delivery_proposal extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','expected_delivery_date',Schema::TYPE_DATETIME . ' NULL COMMENT "Expected delivery date and time" AFTER `delivery_date`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposals}}','expected_delivery_date');
    }
}
