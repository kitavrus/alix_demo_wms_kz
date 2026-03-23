<?php

use yii\db\Migration;

/**
 * Class m190914_101410_ecommerce_cancel_shipment_response
 */
class m190914_101410_ecommerce_cancel_shipment_response extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_cancel_shipment_response', [
            'id' => $this->primaryKey(),
            'cargo_label_request_id' => $this->integer(11)->comment(''),
            'our_outbound_id' => $this->integer(11)->comment(''),
            'cancel_shipment_request_id' => $this->integer(11)->comment(''),
            'IsSuccess' => $this->string(64)->defaultValue('')->comment(''),

            'error_message' => $this->text()->defaultValue('')->comment(''),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_cancel_shipment_response}}');
    }
}
