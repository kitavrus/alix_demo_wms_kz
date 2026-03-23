<?php

use yii\db\Migration;

/**
 * Class m200803_100232_ecommerce_cancel_by_client_outbound_items
 */
class m200803_100232_ecommerce_cancel_by_client_outbound_items extends Migration
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

        $this->createTable('ecommerce_cancel_by_client_outbound_items', [
            'id' => $this->primaryKey(),

            'cancel_by_client_outbound_id' => $this->integer(11)->defaultValue(0)->comment(""),
            'outbound_id' => $this->integer(11)->defaultValue(0)->comment(""),
            'outbound_item_id' => $this->integer(11)->defaultValue(0)->comment(""),
            'stock_id' => $this->integer(11)->defaultValue(0)->comment(""),
            'client_SkuId' => $this->integer(11)->defaultValue(0)->comment(""),
            'product_barcode' => $this->string(36)->defaultValue('')->comment(""),

            'old_box_address' => $this->string(36)->defaultValue('')->comment(""),
            'old_place_address' => $this->string(36)->defaultValue('')->comment(""),
            'new_box_address' => $this->string(36)->defaultValue('')->comment(""),

            'status' => $this->string(36)->defaultValue('')->comment("Статус"),

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
        $this->dropTable('{{%ecommerce_cancel_by_client_outbound_items}}');
        return false;
    }
}
