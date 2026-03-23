<?php

use yii\db\Migration;

/**
 * Class m190715_061046_ecommerce_product
 */
class m190715_061046_ecommerce_product extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_product', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer(11)->defaultValue(0)->comment("Inbound id"),
            'product_on_client_id' => $this->string(64)->defaultValue('')->comment("Product on client id"),
            'product_sku' => $this->string(64)->defaultValue('')->comment("Product sku"),
            'product_name' => $this->string(64)->defaultValue('')->comment("Product name"),
            'product_model' => $this->string(64)->defaultValue('')->comment("Product model"),
            'product_barcode' => $this->string(18)->defaultValue('')->comment("Product model"),
            'status' => $this->smallInteger()->defaultValue(0)->comment("Status"),
            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_product}}');
    }
}
