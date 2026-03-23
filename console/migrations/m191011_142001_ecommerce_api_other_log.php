<?php

use yii\db\Migration;

/**
 * Class m191011_142001_ecommerce_api_other_log
 */
class m191011_142001_ecommerce_api_other_log extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('ecommerce_api_other_log', [
            'id' => $this->primaryKey(),

            'method_name' => $this->string(256)->defaultValue('')->comment("Method name"),

            'request_is_success' => $this->smallInteger()->defaultValue(0)->comment("Response is success"),
            'response_is_success' => $this->smallInteger()->defaultValue(0)->comment("Response is success"),

            'request_data' => $this->text()->defaultValue('')->comment("Request data"),
            'response_data' => $this->text()->defaultValue('')->comment("Response data"),

            'request_error_message' => $this->text()->defaultValue('')->comment("Request error message"),
            'response_error_message' => $this->text()->defaultValue('')->comment("Response error message"),

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
        $this->dropTable('ecommerce_api_other_log');
    }
}