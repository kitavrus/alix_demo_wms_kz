<?php

use yii\db\Migration;

/**
 * Class m190803_100412_ecommerce_employees
 */
class m190803_100412_ecommerce_employees extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_employees', [
            'id' => $this->primaryKey(),
            'barcode' => $this->string(64)->defaultValue('')->comment("Barcode"),
            'first_name' => $this->string(64)->defaultValue('')->comment("First name"),
            'middle_name' => $this->string(64)->defaultValue('')->comment("Middle name"),
            'last_name' => $this->string(64)->defaultValue('')->comment("Last name"),
            'status' => $this->smallInteger()->defaultValue(0)->comment("Status"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_employees}}');
    }
}