<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_route_direction_to_city`.
 */
class m160705_075906_create_table_route_direction_to_city extends Migration
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

        $this->createTable('route_direction_to_city', [
            'id' => $this->primaryKey(),
            'route_direction_id' => $this->integer()->defaultValue(0)->comment("Direction id"),
            'city_id' => $this->integer()->defaultValue(0)->comment("City id"),

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
        $this->dropTable('route_direction_to_city');
    }
}