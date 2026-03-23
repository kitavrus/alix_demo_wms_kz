<?php

use yii\db\Migration;

/**
 * Class m200706_073931_ecommerce_check_box_inventory
 */
class m200706_073931_check_box_inventory extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('check_box_inventory', [
            'id' => $this->primaryKey(),
            'inventory_key' => $this->string(36)->defaultValue('')->comment("Inventory key"),
            'status' => $this->string(36)->defaultValue('')->comment("Статус"),

            'expected_product_qty' => $this->integer(11)->defaultValue(0)->comment("Expected product qty"),
            'scanned_product_qty' => $this->integer(11)->defaultValue(0)->comment("Scanned product qty"),

            'expected_box_qty' => $this->integer(11)->defaultValue(0)->comment("Expected box qty"),
            'scanned_box_qty' => $this->integer(11)->defaultValue(0)->comment("Scanned box qty"),

            'begin_datetime' =>$this->integer(11)->defaultValue(null)->comment("Begin scanning datetime"),
            'end_datetime' =>$this->integer(11)->defaultValue(null)->comment("End scanning datetime"),
            'complete_date' =>$this->integer(11)->defaultValue(null)->comment("Packing date"),

            'description' =>$this->text()->defaultValue(null)->comment("description"),
            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%check_box_inventory}}');
        return false;
    }
}
