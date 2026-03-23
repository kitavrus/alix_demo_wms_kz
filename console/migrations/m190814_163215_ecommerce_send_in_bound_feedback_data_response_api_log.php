<?php

use yii\db\Migration;

/**
 * Class m190814_163215_ecommerce_send_in_bound_feedback_data_response_api_log
 */
class m190814_163215_ecommerce_send_in_bound_feedback_data_response_api_log extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_send_inbound_feedback_data_response', [
            'id' => $this->primaryKey(),
            'our_inbound_id' => $this->integer(11)->comment(''),
            'send_inbound_feedback_data_id' => $this->integer(11)->comment(''),
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
        $this->dropTable('{{%ecommerce_send_inbound_feedback_data_response}}');
    }
}