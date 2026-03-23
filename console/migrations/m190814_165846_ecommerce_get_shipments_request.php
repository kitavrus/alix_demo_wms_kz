<?php

use yii\db\Migration;

/**
 * Class m190814_165846_ecommerce_get_shipments_request
 */
class m190814_165846_ecommerce_get_shipments_request extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_get_shipments_request', [
            'id' => $this->primaryKey(),
            'BusinessUnitId' => $this->string(64)->defaultValue('')->comment(''),
            'OrderQuantity' => $this->integer(11)->defaultValue(0)->comment(''),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_get_shipments_request}}');
    }
}
