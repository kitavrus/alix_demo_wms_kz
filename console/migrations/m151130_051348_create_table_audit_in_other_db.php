<?php

use yii\db\Schema;
use yii\db\Migration;

class m151130_051348_create_table_audit_in_other_db extends Migration
{
    public function init()
    {
        $this->db = 'dbAudit';
        parent::init();
    }

    public function up()
    {
//        return true;
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%cross_dock_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%cross_dock_items_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%inbound_orders_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%inbound_order_items_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%outbound_orders_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%outbound_order_items_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%outbound_picking_lists_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%stock_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%store_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%store_reviews_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%tl_agents_billing_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%tl_agents_billing_conditions_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%tl_agents_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%tl_delivery_proposals_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%tl_delivery_proposal_billing_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%tl_delivery_proposal_billing_conditions_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%tl_delivery_proposal_orders_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%tl_delivery_proposal_route_cars_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%tl_delivery_proposal_routes_car_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%tl_delivery_proposal_route_unforeseen_expenses_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%tl_delivery_proposal_routes_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%tl_outbound_registry_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%tl_outbound_registry_items_audit}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified object id"',
            'date_created' => Schema::TYPE_DATETIME . ' NULL COMMENT "Modification timestamp"',
            'created_by' => Schema::TYPE_INTEGER . ' NULL COMMENT "Modified user_id"',
            'field_name' => Schema::TYPE_STRING. ' NULL COMMENT "Modified object attribute name"',
            'before_value_text' => Schema::TYPE_STRING . ' NULL COMMENT "Value of attribute before modification"',
            'after_value_text' => Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'",
        ], $tableOptions);

        $this->createTable('{{%cross_dock_log}}', [
            'id' => Schema::TYPE_PK,
            'unique_key' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Unique key if update exist order"',
            'client_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Client store id"',
            'box_barcode' => Schema::TYPE_STRING . '(54) NULL DEFAULT "" COMMENT "Scanned box barcode"',
            'from_point_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "From point id"',
            'to_point_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "To point id"',
            'to_point_title' => Schema::TYPE_STRING . ' NULL DEFAULT "" COMMENT "To point title"',
            'from_point_title' => Schema::TYPE_STRING . ' NULL DEFAULT "" COMMENT "From point title"',

            'party_number' => Schema::TYPE_STRING . '(128) NULL DEFAULT "" COMMENT "Party number, received from the client"',
            'order_number' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Inbound order number"',
            'order_type' => Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" COMMENT "Party type: stock, cross-doc, etc"',
            'status' => Schema::TYPE_SMALLINT . " NULL DEFAULT '0' COMMENT 'Status new, in process, complete, etc'",

            'expected_number_places_qty' => Schema::TYPE_INTEGER . '(11) NULL COMMENT "Expected number places quantity in party"',
            'expected_rpt_places_qty' =>Schema::TYPE_INTEGER . '(11) NULL DEFAULT 0 COMMENT "Expected rpt places qty"',

            'box_m3' => Schema::TYPE_STRING . '(32) NULL DEFAULT 0 COMMENT "Box size m3"',
            'weight_net' => Schema::TYPE_STRING . '(32) NULL DEFAULT 0 COMMENT "Box net weight"',
            'weight_brut' => Schema::TYPE_STRING . '(32) NULL DEFAULT 0 COMMENT "Box brut weight"',
            'field_extra' =>  Schema::TYPE_TEXT . ' NULL DEFAULT "" comment "Записываем содержимое (товары) короба"',

            'expected_datetime' => Schema::TYPE_INTEGER . ' NULL COMMENT "The expected date of delivery in stock"',

            'begin_datetime' => Schema::TYPE_INTEGER . ' NULL COMMENT "The start time of the scan party"',
            'end_datetime' => Schema::TYPE_INTEGER . ' NULL COMMENT "The end time of the scan party"',

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NULL DEFAULT 0',

            'created_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0',
            'deleted' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0',

        ], $tableOptions);

        $this->createTable('{{%inbound_upload_log}}', [
            'id' => Schema::TYPE_PK,
            'client_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "Client id"',

            'unique_key' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Unique key if update exist order"',
            'order_number' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Inbound order number"',
            'product_barcode' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Product barcode"',
            'product_model' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Product model"',
            'expected_qty' => Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" COMMENT "Expected qty"',
            'order_type' => Schema::TYPE_SMALLINT . '(2) NULL DEFAULT "0" COMMENT "Type: from stock, cross-dock"',
            'delivery_type' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0 COMMENT "CROSS-DOCK, RPT, etc ... "',

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_SMALLINT . ' DEFAULT 0',
        ], $tableOptions);

        $this->createTable('{{%outbound_upload_items_log}}', [
            'id' => Schema::TYPE_PK,
            'outbound_upload_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Internal outbound order upload id"',

            'product_id' => Schema::TYPE_INTEGER . ' NULL COMMENT "Internal product id"',
            'product_name' => Schema::TYPE_STRING . '(128)  NULL COMMENT "Scanned product name"',
            'product_barcode' => Schema::TYPE_STRING . '(54)  NULL COMMENT "Scanned product barcode"',
            'product_price' => Schema::TYPE_DECIMAL . '(16,3) NULL COMMENT "Product price"',
            'product_model' => Schema::TYPE_STRING . '(128) NULL COMMENT "Product model"',
            'product_sku' => Schema::TYPE_STRING . '(128) NULL COMMENT "Product sku"',
            'product_madein' => Schema::TYPE_STRING . '(128) NULL COMMENT "Product made in"',
            'product_composition' => Schema::TYPE_STRING . '(128) NULL COMMENT "Product composition"',
            'product_exporter' => Schema::TYPE_TEXT . ' NULL COMMENT "Product exporter"',
            'product_importer' => Schema::TYPE_TEXT . ' NULL COMMENT "Product importer"',
            'product_description' => Schema::TYPE_TEXT . ' NULL COMMENT "Product importer"',
            'product_serialize_data' => Schema::TYPE_TEXT . ' NULL COMMENT "Product serialize data"',

            'box_barcode' => Schema::TYPE_STRING . '(54)  NULL COMMENT "Box barcode"',

            'status' => Schema::TYPE_SMALLINT . "  NULL DEFAULT '0' COMMENT 'Status new, scanned'",

            'expected_qty' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0" COMMENT "Expected product quantity in order"',
            'accepted_qty' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0" COMMENT "Accepted product quantity in order"',

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',

            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0',

        ], $tableOptions);

        $this->createTable('{{%outbound_upload_log}}', [
            'id' => Schema::TYPE_PK,
            'client_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT 0 COMMENT "Client id"',

            'unique_key' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Unique key if update exist order"',
            'party_number' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Outbound party number"',
            'order_number' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Outbound order number"',
            'product_barcode' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Product barcode"',
            'product_model' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Product model"',
            'expected_qty' => Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" COMMENT "Expected qty"',

            'from_point_id' => Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" COMMENT  "Internal from point id"',
            'to_point_id' => Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" COMMENT "Internal to point id"',

            'to_point_title' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Product model"',
            'from_point_title' => Schema::TYPE_STRING . '(34) NULL DEFAULT "" COMMENT "Product model"',
            'order_type' => Schema::TYPE_SMALLINT . '(2) NULL DEFAULT "0" COMMENT "Type: from stock, cross-dock"',
            'delivery_type' => Schema::TYPE_SMALLINT . '(2) NULL DEFAULT "0" COMMENT "CROSS-DOCK, RPT, etc ..."',

            'data_created_on_client' => Schema::TYPE_STRING . '(64)  NULL DEFAULT "" comment "Date time created order on client"',
            'field_extra' =>  Schema::TYPE_TEXT . ' NULL DEFAULT "" COMMENT "Example JSON: order_number, who received order, etc ..."',

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'created_at' => Schema::TYPE_INTEGER . '  NULL',
            'updated_at' => Schema::TYPE_INTEGER . '  NULL',
            'deleted' => Schema::TYPE_SMALLINT . ' DEFAULT 0',
        ], $tableOptions);

        $this->createTable('{{%bookkeeper_audit}}', [
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
        $this->dropTable('{{%cross_dock_audit}}');
        $this->dropTable('{{%cross_dock_items_audit}}');
        $this->dropTable('{{%inbound_orders_audit}}');
        $this->dropTable('{{%inbound_order_items_audit}}');
        $this->dropTable('{{%outbound_orders_audit}}');
        $this->dropTable('{{%outbound_order_items_audit}}');
        $this->dropTable('{{%outbound_picking_lists_audit}}');
        $this->dropTable('{{%stock_audit}}');
        $this->dropTable('{{%store_audit}}');
        $this->dropTable('{{%store_reviews_audit}}');
        $this->dropTable('{{%tl_agents_billing_audit}}');
        $this->dropTable('{{%tl_agents_billing_conditions_audit}}');
        $this->dropTable('{{%tl_agents_audit}}');
        $this->dropTable('{{%tl_delivery_proposals_audit}}');
        $this->dropTable('{{%tl_delivery_proposal_billing_audit}}');
        $this->dropTable('{{%tl_delivery_proposal_billing_conditions_audit}}');
        $this->dropTable('{{%tl_delivery_proposal_orders_audit}}');
        $this->dropTable('{{%tl_delivery_proposal_route_cars_audit}}');
        $this->dropTable('{{%tl_delivery_proposal_routes_car_audit}}');
        $this->dropTable('{{%tl_delivery_proposal_route_unforeseen_expenses_audit}}');
        $this->dropTable('{{%tl_delivery_proposal_routes_audit}}');
        $this->dropTable('{{%tl_outbound_registry_audit}}');
        $this->dropTable('{{%tl_outbound_registry_items_audit}}');
        $this->dropTable('{{%bookkeeper_audit}}');

        $this->dropTable('{{%cross_dock_log}}');
        $this->dropTable('{{%inbound_upload_log}}');
        $this->dropTable('{{%outbound_upload_items_log}}');
        $this->dropTable('{{%outbound_upload_log}}');
    }
}