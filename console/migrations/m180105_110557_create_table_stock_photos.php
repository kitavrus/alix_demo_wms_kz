<?php

use yii\db\Migration;

/**
 * Class m180105_110557_add_field_image_table_stock
 */
class m180105_110557_create_table_stock_photos extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('stock_photos', [
            'id' => $this->primaryKey(),
            'stock_id' => $this->integer(11)->defaultValue(0)->comment("Stock id"),

            'is_type' =>$this->integer(11)->defaultValue(0)->comment("Type product image, damage image"),
            'path_to_photo' => $this->string(512)->defaultValue('')->comment("Path to image"),

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
        $this->dropTable('stock_photos');
    }
}