<?php

use yii\db\Migration;

/**
 * Class m200803_080854_ecommerce_cancel_by_client_outbound
 */
class m200803_080854_ecommerce_cancel_by_client_outbound extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_cancel_by_client_outbound', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer(11)->defaultValue(0)->comment(""),
            'outbound_id' => $this->integer(11)->defaultValue(0)->comment(""),

            'cancel_key' => $this->string(36)->defaultValue('')->comment(""),
            'order_number' => $this->string(36)->defaultValue('')->comment(""),
            'outbound_box' => $this->string(36)->defaultValue('')->comment(""),
            'client_OrderSource' => $this->string(36)->defaultValue('')->comment(""),
            'new_box_address' => $this->string(36)->defaultValue('')->comment(""),

            'status' => $this->string(36)->defaultValue('')->comment("Статус"),
            'api_status' => $this->string(36)->defaultValue('')->comment("API cтатус"),

            'expected_qty' => $this->integer(11)->defaultValue(0)->comment("Expected qty"),
            'accepted_qty' => $this->integer(11)->defaultValue(0)->comment("Accepted qty"),

            'begin_datetime' =>$this->integer(11)->defaultValue(null)->comment("Begin scanning datetime"),
            'end_datetime' =>$this->integer(11)->defaultValue(null)->comment("End scanning datetime"),

            'date_confirm' =>$this->integer(11)->defaultValue(null)->comment("Confirm datetime"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%ecommerce_cancel_by_client_outbound}}');
        return false;
    }
}
