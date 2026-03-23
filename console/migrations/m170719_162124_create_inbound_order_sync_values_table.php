<?php

use yii\db\Migration;

/**
 * Handles the creation for table `inbound_order_sync_values_table`.
 */
class m170719_162124_create_inbound_order_sync_values_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('inbound_order_sync_values', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer(11)->defaultValue(0)->comment("Client id"),

            'inbound_id' => $this->integer(11)->defaultValue(0)->comment("Inbound id"),
            'inbound_client_id' => $this->string(128)->defaultValue('')->comment("Client inbound id"),

            'status_our' => $this->smallInteger()->defaultValue(null)->comment("Status our"),
            'status_client' => $this->smallInteger()->defaultValue(null)->comment("Status client"),

            'zone_our' => $this->string(64)->defaultValue("")->comment("Zone our"),
            'zone_client' => $this->string(64)->defaultValue("")->comment("Zone client"),

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
        $this->dropTable('inbound_order_sync_values');
    }
}