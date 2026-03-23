<?php

use yii\db\Migration;

/**
 * Class m190831_172745_ecommerce_get_lot_content_response
 */
class m190831_172745_ecommerce_get_lot_content_response extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_get_lot_content_response', [
            'id' => $this->primaryKey(),
            'our_inbound_id' => $this->integer(11)->comment(''),
            'get_lot_content_id' => $this->integer(11)->comment(''),

            'LotBarcode' => $this->string(64)->defaultValue('')->comment(''),
            'ProductBarcode' => $this->string(64)->defaultValue('')->comment(''),
            'Quantity' => $this->string(64)->defaultValue('')->comment(''),

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
        $this->dropTable('{{%ecommerce_get_lot_content_response}}');
    }
}