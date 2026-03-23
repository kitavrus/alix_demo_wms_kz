<?php

use yii\db\Migration;

/**
 * Class m190914_101439_ecommerce_stock_adjustment_response
 */
class m190914_101439_ecommerce_stock_adjustment_request extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_stock_adjustment_request', [
            'id' => $this->primaryKey(),
            'BusinessUnitId' => $this->string(64)->defaultValue('')->comment(''),
            'LotOrSingleBarcode' => $this->string(64)->defaultValue('')->comment(''),
            'Quantity' => $this->string(64)->defaultValue('')->comment(''),
            'Operator' => $this->string(64)->defaultValue('')->comment(''),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_stock_adjustment_request}}');
    }
}