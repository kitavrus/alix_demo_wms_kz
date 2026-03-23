<?php

use yii\db\Migration;

/**
 * Handles the creation for table `sheep_shipment_place_address_table`.
 */
class m170622_115744_create_sheep_shipment_place_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('sheep_shipment_place_address', [
            'id' => $this->primaryKey(),
            'address' => $this->string(128)->defaultValue('')->comment("Place address"),

            'created_user_id' =>$this->integer()->defaultValue(0)->comment("Created user id"),
            'updated_user_id' =>$this->integer()->defaultValue(0)->comment("Updated user id"),

            'created_at' =>$this->integer()->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer()->defaultValue(null)->comment("Updated at"),

            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('sheep_shipment_place_address');
    }
}
