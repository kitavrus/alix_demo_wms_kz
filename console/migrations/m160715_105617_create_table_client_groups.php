<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_client_groups`.
 */
class m160715_105617_create_table_client_groups extends Migration
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

        $this->createTable('client_groups', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->defaultValue(0)->comment("Group name"),
            'status' => $this->smallInteger()->defaultValue(0)->comment("Status active, no active"),
            'base_type' => $this->smallInteger()->defaultValue(0)->comment("Type: base,custom"),

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
        $this->dropTable('client_groups');
    }
}