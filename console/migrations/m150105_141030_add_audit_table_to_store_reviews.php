<?php

use yii\db\Schema;
use yii\db\Migration;

class m150105_141030_add_audit_table_to_store_reviews extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%store_reviews_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

    }

    public function down()
    {
        echo "m150105_141030_add_audit_table_to_store_reviews cannot be reverted.\n";

        return false;
    }
}
