<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_client_group_to_client`.
 */
class m160715_105628_create_table_client_group_to_client extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('client_group_to_client', [
            'id' => $this->primaryKey(),
            'client_group_id' => $this->integer()->defaultValue(0)->comment("Client group id"),
            'client_id' => $this->integer()->defaultValue(0)->comment("Client id"),

            'created_user_id' => $this->integer()->defaultValue(0),
            'updated_user_id' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->defaultValue(0),
            'updated_at' => $this->integer()->defaultValue(0),
            'deleted' => $this->integer()->defaultValue(0),
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('client_group_to_client');
    }
}