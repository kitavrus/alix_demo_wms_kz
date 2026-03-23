<?php

use yii\db\Schema;
use yii\db\Migration;

class m150813_065458_add_tl_outbound_registry_and_items extends Migration
{
    public function up()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tl_outbound_registry}}', [
            'id' => Schema::TYPE_PK,
            'agent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Agent id"',
            'car_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Car id"',
            'driver_name' => Schema::TYPE_STRING . ' NULL COMMENT "Driver name"',
            'driver_phone' => Schema::TYPE_STRING . ' NULL COMMENT "Driver phone"',
            'driver_auto_number' => Schema::TYPE_STRING . ' NULL COMMENT "Auto number"',
            'weight' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT 0 comment "kg"',
            'volume' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT 0 comment "Meters cubic"',
            'places' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "Places"',
            'extra_fields' => Schema::TYPE_TEXT . ' NULL DEFAULT ""',
            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'created_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'deleted' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0',

        ], $tableOptions);

        $this->createTable('{{%tl_outbound_registry_items}}', [
            'id' => Schema::TYPE_PK,
            'tl_outbound_registry_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "registry id"',
            'tl_delivery_proposal_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "dp id"',
            'route_from' => Schema::TYPE_INTEGER . ' NULL COMMENT "store id from"',
            'route_to' => Schema::TYPE_INTEGER . ' NULL COMMENT "store id to"',
            'weight' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT 0 comment "kg"',
            'volume' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT 0 comment "Meters cubic"',
            'places' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "Places"',
            'extra_fields' => Schema::TYPE_TEXT . ' NULL DEFAULT ""',
            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'created_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'deleted' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0',

        ], $tableOptions);


    }

    public function down()
    {
        $this->dropTable('tl_outbound_registry');
        $this->dropTable('tl_outbound_registry_items');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}