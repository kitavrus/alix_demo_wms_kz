<?php

use yii\db\Migration;

/**
 * Class m190911_140130_ecommerce_barcode_manager
 */
class m190911_140130_ecommerce_barcode_manager extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('ecommerce_barcode_manager', [
            'id' => $this->primaryKey(),
            'barcode_prefix' => $this->string(5)->defaultValue('')->comment("Barcode prefix"),
            'title' => $this->string(256)->defaultValue('')->comment("Title"),
            'counter' =>$this->integer(11)->defaultValue(0)->comment("Counter"),
            'status' =>$this->smallInteger()->defaultValue(1)->comment("Status"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('ecommerce_barcode_manager');
    }
}