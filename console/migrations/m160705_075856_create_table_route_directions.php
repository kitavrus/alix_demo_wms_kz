<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_route_directions`.
 */
class m160705_075856_create_table_route_directions extends Migration
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

        $this->createTable('route_directions', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->defaultValue(0)->comment("Direction name"),
            'status' => $this->smallInteger()->defaultValue(0)->comment("Status active, no active"),

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
        $this->dropTable('route_directions');
    }
}