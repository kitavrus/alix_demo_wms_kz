<?php

use yii\db\Migration;

/**
 * Class m250616_164000_add_table_api_log
 */
class m250616_164000_add_table_api_log extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('api_logs', [
            'id' => $this->primaryKey(),
            'our_order_id' => $this->integer()->defaultValue(0)->comment("Our in/out/return id"),
            'their_order_number' => $this->string(256)->defaultValue(0)->comment("Their in/out/return number"),
            'method_name' => $this->string(256)->defaultValue('')->comment("Method name"),
            'order_type' => $this->string(256)->defaultValue('')->comment("in/out/return b2b or b2c"),
            'request_status' => $this->string(256)->defaultValue('')->comment("Request Status"),
            'request_data' => $this->text()->defaultValue('')->comment("Request data"),
            'response_data' => $this->text()->defaultValue('')->comment("Response data"),
			'response_code' => $this->smallInteger()->defaultValue(0)->comment("Response code"),
            'response_message' => $this->text()->defaultValue('')->comment("Response error message"),
            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('api_logs');
    }
}
