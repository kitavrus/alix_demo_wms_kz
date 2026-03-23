<?php

use yii\db\Migration;

/**
 * Class m190814_172248_ecommerce_send_shipment_feedback_response
 */
class m190814_172248_ecommerce_send_shipment_feedback_response extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_send_shipment_feedback_response', [
            'id' => $this->primaryKey(),
            'our_outbound_id' => $this->integer(11)->defaultValue(0)->comment(''),

            'send_shipment_feedback_id' => $this->integer(11)->comment(''),
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
        $this->dropTable('{{%ecommerce_send_shipment_feedback_response}}');
    }
}
