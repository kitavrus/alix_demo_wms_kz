<?php

use yii\db\Schema;
use yii\db\Migration;

class m141011_100805_add_audit_tables extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tl_delivery_proposals_audit}}', [
                'id' => Schema::TYPE_PK,
                'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
                'date_created' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modification timestamp"',
                'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
                'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
                'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
                'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
            ], $tableOptions);

        $this->createTable('{{%tl_delivery_proposal_routes_audit}}', [
                'id' => Schema::TYPE_PK,
                'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
                'date_created' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modification timestamp"',
                'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
                'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
                'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
                'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
            ], $tableOptions);

        $this->createTable('{{%tl_delivery_proposal_routes_car_audit}}', [
                'id' => Schema::TYPE_PK,
                'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
                'date_created' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modification timestamp"',
                'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
                'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
                'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
                'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
            ], $tableOptions);

        $this->createTable('{{%tl_delivery_proposal_route_cars_audit}}', [
                'id' => Schema::TYPE_PK,
                'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
                'date_created' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modification timestamp"',
                'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
                'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
                'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
                'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
            ], $tableOptions);

        $this->createTable('{{%tl_delivery_proposal_route_unforeseen_expenses_audit}}', [
                'id' => Schema::TYPE_PK,
                'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
                'date_created' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modification timestamp"',
                'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
                'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
                'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
                'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
            ], $tableOptions);
    }

    public function down()
    {
        echo "m141011_100805_add_audit_tables cannot be reverted.\n";

        return false;
    }
}
