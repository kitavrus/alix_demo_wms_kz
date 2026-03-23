<?php

use yii\db\Migration;

/**
 * Class m200706_073948_ecommerce_check_box
 */
class m200706_073948_check_box extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('check_box', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer(11)->defaultValue(0)->comment("Client id"),
            'warehouse_id' => $this->integer(11)->defaultValue(0)->comment("Warehouse id"),
            'employee_id' => $this->integer(11)->defaultValue(0)->comment("Employee id"),
            'inventory_id' => $this->integer(11)->defaultValue(0)->comment("Inventory id"),

            'box_barcode' => $this->string(18)->defaultValue('')->comment("Box barcode"),
            'place_address' => $this->string(18)->defaultValue('')->comment("Place address barcode"),
            'place_address_part1' => $this->string(5)->defaultValue('')->comment("Place address floor"),
            'place_address_part2' => $this->string(5)->defaultValue('')->comment("Place address box"),
            'place_address_part3' => $this->string(5)->defaultValue('')->comment("Place address place"),
            'place_address_part4' => $this->string(5)->defaultValue('')->comment("Place address level"),
            'place_address_part5' => $this->string(5)->defaultValue('')->comment("Place address other"),

            'expected_qty' => $this->integer(11)->defaultValue(0)->comment("Expected Quantity"),
            'scanned_qty' => $this->integer(11)->defaultValue(0)->comment("Scanned Quantity"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);

        $this->execute("ALTER TABLE `check_box`
ADD INDEX `client_id` (`client_id`),
ADD INDEX `warehouse_id` (`warehouse_id`),
ADD INDEX `box_barcode` (`box_barcode`),
ADD INDEX `place_address` (`place_address`);");
    }

    public function safeDown()
    {
        $this->dropTable('{{%check_box}}');
    }
}
