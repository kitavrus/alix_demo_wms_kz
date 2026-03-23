<?php

use yii\db\Schema;
use yii\db\Migration;

class m150709_061948_add_outboud_box_labels extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%outbound_box_labels}}', [
            'id' => Schema::TYPE_PK,
            'client_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Client store id"',
            'outbound_order_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Outbound Order id"',
            'outbound_order_number' => Schema::TYPE_STRING . ' NULL COMMENT "Outbound Order Number"',
            'box_label_url' => Schema::TYPE_STRING . ' NULL COMMENT "url"',
            'filename' => Schema::TYPE_STRING . ' NULL COMMENT "file name"',
            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'created_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'deleted' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%outbound_box_labels}}');
    }
}
