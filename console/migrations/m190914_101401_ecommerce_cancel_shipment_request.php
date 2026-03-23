<?php

use yii\db\Migration;

/**
 * Class m190914_101401_ecommerce_cancel_shipment_request
 */
class m190914_101401_ecommerce_cancel_shipment_request extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_cancel_shipment_request', [
            'id' => $this->primaryKey(),
            'our_outbound_id' => $this->integer(11)->comment(''),
            'BusinessUnitId' => $this->string(64)->defaultValue('')->comment(''),
            'ShipmentId' => $this->string(64)->defaultValue('')->comment(''),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_cancel_shipment_request}}');
    }
}