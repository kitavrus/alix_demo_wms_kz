<?php

use yii\db\Migration;

/**
 * Class m191016_153229_ecommerce_outbound_list
 */
class m191016_153229_ecommerce_outbound_list extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('ecommerce_outbound_list', [
            'id' => $this->primaryKey(),
            'our_outbound_id' => $this->integer()->defaultValue(0)->comment("Our outbound id"),
            'list_title' => $this->string(36)->defaultValue('')->comment("List title"),
            'package_barcode' => $this->string(36)->defaultValue('')->comment("Package barcode"),
            'status' => $this->smallInteger()->defaultValue(0)->comment("Status"),

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
        $this->dropTable('ecommerce_outbound_list');
    }
}
