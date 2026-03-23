<?php

use yii\db\Schema;
use yii\db\Migration;

class m150327_125624_add_column_product_model_table_return_order_items extends Migration
{
    public function up()
    {
        $this->addColumn('{{%return_order_items}}','product_model',Schema::TYPE_STRING . '(128) NULL DEFAULT "" COMMENT "Product model (article)" AFTER `product_barcode`');
    }

    public function down()
    {
        $this->dropColumn('{{%return_order_items}}','product_model');
    }
}
