<?php

use yii\db\Migration;

/**
 * Class m200328_101833_ecommerce_stock_adjustment
 */
class m200328_101833_ecommerce_stock_adjustment extends Migration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_stock_adjustment', [
            'id' => $this->primaryKey(),
            'product_barcode' => $this->string(36)->defaultValue('')->comment("Шк товара"),
            'product_quantity' => $this->smallInteger()->defaultValue(0)->comment("Количество"),
            'product_operator' => $this->string(1)->defaultValue('')->comment("Оператор +-"),
            'reason' => $this->text()->defaultValue('')->comment("Причина"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);

        $this->addColumn('{{%ecommerce_stock}}', 'stock_adjustment_id', $this->integer()->defaultValue(0)->comment("stock adjustment id")->after('return_id'));
        $this->addColumn('{{%ecommerce_stock}}', 'stock_adjustment_status', $this->integer()->defaultValue(0)->comment("stock adjustment status")->after('stock_adjustment_id'));

    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_stock_adjustment}}');
        $this->dropColumn('{{%ecommerce_stock}}', 'stock_adjustment_id');
        $this->dropColumn('{{%ecommerce_stock}}', 'stock_adjustment_status');
    }
}
