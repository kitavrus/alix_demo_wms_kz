<?php

use yii\db\Migration;

class m171121_101446_alter_fields_product_model_table_outbound_order_items extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE outbound_order_items ADD INDEX product_model (product_model);");
    }

    public function down()
    {
        return false;
    }
}
