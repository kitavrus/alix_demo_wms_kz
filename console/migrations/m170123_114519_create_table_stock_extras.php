<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_stock_extras`.
 */
class m170123_114519_create_table_stock_extras extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('stock_extra_fields', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->defaultValue(0)->comment("Stock id"),
            'field_name' => $this->string(128)->defaultValue(0)->comment("Field name"),
            'field_value' => $this->string(256)->defaultValue(0)->comment("Field value"),
            'date_created' => $this->integer()->defaultValue(0)->comment("Date created"),
            'created_by' => $this->integer()->defaultValue(0)->comment("Created by"),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('stock_extra_fields');
    }
}