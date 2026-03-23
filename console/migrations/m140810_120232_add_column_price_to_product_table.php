<?php

use yii\db\Schema;
use yii\db\Migration;

class m140810_120232_add_column_price_to_product_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%product}}','price',Schema::TYPE_DECIMAL . '(26,6) NULL COMMENT "Product price" AFTER `status`');
    }

    public function down()
    {
        $this->dropColumn('{{%product}}','price');
    }
}
