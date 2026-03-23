<?php

use yii\db\Schema;
use yii\db\Migration;

class m150408_111354_add_agents_billing_and_agents_billing_audit extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tl_agents_billing}}', [
            'id' => Schema::TYPE_PK,
            'agent_id' => Schema::TYPE_INTEGER . ' NULL comment "Agent id"',

            'from_country_id' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0"',
            'from_region_id' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0"',
            'from_city_id' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0"',

            'to_country_id' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0"',
            'to_region_id' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0"',
            'to_city_id' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0"',

            'route_from' => Schema::TYPE_INTEGER . '  NULL comment "Example: DC-APORT"',
            'route_to' => Schema::TYPE_INTEGER . '  NULL comment "Example: DC-APORT"',
            'rule_type' => Schema::TYPE_SMALLINT . '  NULL DEFAULT "0"',

            'mc' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Meters cubic"',// Метры кубические. обычный инпут
            'kg' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Kilogram"', // Киллограмы. обычный инпут

            'number_places' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0" comment "Estimated number palaces"', // Перполагаемое количество мест. обычный инпут

            'price_invoice' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Sale for client"', // Цена которую должен оплатить клиент за оказанную услугу. обычный инпут
            'price_invoice_with_vat' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0"', // Цена с НДС которую должен оплатить клиент за оказанную услугу. обычный инпут
            'price_invoice_kg' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0"',
            'price_invoice_kg_with_vat' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0"',
            'price_invoice_mc' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0"',
            'price_invoice_mc_with_vat' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0"',

            'formula_tariff' => Schema::TYPE_TEXT . ' NULL comment "Formula for tariff"', // Формула для вычисления тарифа

            'status' => Schema::TYPE_SMALLINT . '  NULL DEFAULT 0', // Статус, выпадающий писок. Привет значенией : Активен не активен
            'delivery_term' => Schema::TYPE_STRING . ' NULL', // Статус, выпадающий писок. Привет значенией : Активен не активен
            'delivery_term_from' => Schema::TYPE_SMALLINT . ' NULL', // Статус, выпадающий писок. Привет значенией : Активен не активен
            'delivery_term_to' => Schema::TYPE_SMALLINT . ' NULL', // Статус, выпадающий писок. Привет значенией : Активен не активен
            'tariff_type' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0', // Статус, выпадающий писок. Привет значенией : Активен не активен
            'cooperation_type' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0', // Статус, выпадающий писок. Привет значенией : Активен не активен
            'delivery_type' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0', // Статус, выпадающий писок. Привет значенией : Активен не активен
            'comment' => Schema::TYPE_TEXT . '  NULL',

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',// Это заполняется через бихейвер
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL', // Это заполняется через бихейвер

            'created_at' => Schema::TYPE_INTEGER . ' NULL', // Это заполняется через бихейвер
            'updated_at' => Schema::TYPE_INTEGER . ' NULL', // Это заполняется через бихейвер
            'deleted' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0', // Это заполняется через бихейвер
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

        $this->createTable('{{%tl_agents_billing_conditions}}', [
            'id' => Schema::TYPE_PK,
            'tl_agents_billing_id' => Schema::TYPE_INTEGER . ' NULL', // Тут выбадающий список и клиентов
            'agent_id' => Schema::TYPE_INTEGER . ' NULL', // Тут выбадающий список и клиентов

            'price_invoice' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Sale for client"', // Цена которую должен оплатить клиент за оказанную услугу. обычный инпут
            'price_invoice_with_vat' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Price invoice with NDS"', // Цена с НДС которую должен оплатить клиент за оказанную услугу. обычный инпут

            'formula_tariff' => Schema::TYPE_TEXT . ' NULL DEFAULT "" comment "Formula for tariff"', // Формула для вычисления тарифа

            'status' => Schema::TYPE_SMALLINT . '  NULL DEFAULT 0',
            'comment' => Schema::TYPE_TEXT . '  NULL',
            'title' => Schema::TYPE_TEXT . '  NULL',
            'delivery_type' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0',
            'sort_order' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0',
            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL',

            'created_at' => Schema::TYPE_INTEGER . ' NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL',
            'deleted' => Schema::TYPE_SMALLINT . ' DEFAULT 0',
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

    }

    public function down()
    {
       $this->dropTable('tl_agents_billing');
       $this->dropTable('tl_agents_billing_audit');
       $this->dropTable('tl_agents_billing_conditions');
       $this->dropTable('tl_agents_billing_conditions_audit');
    }
}
