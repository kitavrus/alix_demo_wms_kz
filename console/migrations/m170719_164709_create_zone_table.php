<?php

use yii\db\Migration;

/**
 * Handles the creation for table `zone_table`.
 */
class m170719_164709_create_zone_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('stock_zone', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->defaultValue("")->comment("Name"),

            'address_begin' => $this->string(64)->defaultValue("")->comment("Address begin"),
            'address_end' => $this->string(64)->defaultValue("")->comment("Address end"),

            'created_user_id' =>$this->integer()->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer()->defaultValue(null)->comment("Updated user id"),

            'created_at' =>$this->integer()->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer()->defaultValue(null)->comment("Updated at"),

            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('stock_zone');
    }
}