<?php

use yii\db\Schema;
use yii\db\Migration;

class m141003_111837_create_table_store_reviews extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%store_reviews}}', [
            'id' => Schema::TYPE_PK,
            'client_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Client store id"',
            'store_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Store id"',
            'tl_delivery_proposal_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Delivery proposal id"',

            'delivery_datetime' => Schema::TYPE_INTEGER . ' NULL COMMENT "Delivered date time"',

            'rate' => Schema::TYPE_SMALLINT . ' NULL COMMENT "Rate, 1-star,2-star ... 5-star "',
            'comment' => Schema::TYPE_STRING . ' NULL COMMENT "Review text"',

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL',

            'created_at' => Schema::TYPE_INTEGER . ' NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL',

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%store_reviews}}');
    }
}
