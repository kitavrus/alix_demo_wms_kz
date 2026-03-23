<?php

use yii\db\Migration;

/**
 * Handles the creation for table `sheep_shipment_table`.
 */
class m170622_115805_create_sheep_shipment_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('sheep_shipment', [
            'id' => $this->primaryKey(),
            'place_address' => $this->string(64)->defaultValue('')->comment("Place address"),
            'box_barcode' => $this->string(64)->defaultValue('')->comment("Box barcode"),

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
        $this->dropTable('sheep_shipment');
    }
}
