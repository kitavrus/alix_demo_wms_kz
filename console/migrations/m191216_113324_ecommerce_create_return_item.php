<?php

use yii\db\Migration;

/**
 * Class m191216_113324_ecommerce_create_return_item
 */
class m191216_113324_ecommerce_create_return_item extends Migration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_return_items', [
            'id' => $this->primaryKey(),
            'return_id' => $this->integer(11)->defaultValue(0)->comment("Return id"),
            'product_id' => $this->integer(11)->defaultValue(0)->comment("Product id"),

            'product_barcode' => $this->string(18)->defaultValue('')->comment("Шк товара"),
            'expected_qty' => $this->integer(11)->defaultValue(0)->comment("Product Expected qty"),
            'accepted_qty' => $this->integer(11)->defaultValue(0)->comment("Product Accepted qty"),

            'status' => $this->smallInteger()->defaultValue(0)->comment("Status"),

            'begin_datetime' =>$this->integer(11)->defaultValue(null)->comment("Begin scanning datetime"),
            'end_datetime' =>$this->integer(11)->defaultValue(null)->comment("End scanning datetime"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_return_items}}');
    }
}