<?php

use yii\db\Schema;
use yii\db\Migration;

class m141107_092519_create_table_tl_delivery_proposal_billing_conditions extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tl_delivery_proposal_billing_conditions}}', [
            'id' => Schema::TYPE_PK,
            'tl_delivery_proposal_billing_id' => Schema::TYPE_INTEGER . ' NULL comment "Example: DeTacty. Internal client id"', // Тут выбадающий список и клиентов
            'client_id' => Schema::TYPE_INTEGER . ' NULL comment "Example: DeTacty. Internal client id"', // Тут выбадающий список и клиентов

//            'country_id' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0" comment "Country id"', // Страна
//            'region_id' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0" comment "Region id"', // Регион
//            'city_id' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0" comment "City id"', // Город
//
//            'route_from' => Schema::TYPE_INTEGER . '  NULL comment "Example: DC-APORT"', // Тут выпадающий список из доступных путей для этого клиента и возможность добавить новый пунк доставки
//            'route_to' => Schema::TYPE_INTEGER . '  NULL comment "Example: DC-APORT"', // Тут выпадающий список из доступных путей для этого клиента и возможность добавить новый пунк доставки
//
//            'mc' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Meters cubic"',// Метры кубические. обычный инпут
//            'kg' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Kilogram"', // Киллограмы. обычный инпут
//
//            'number_places' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0" comment "Estimated number palaces"', // Перполагаемое количество мест. обычный инпут

            'price_invoice' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Sale for client"', // Цена которую должен оплатить клиент за оказанную услугу. обычный инпут
            'price_invoice_with_vat' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Price invoice with NDS"', // Цена с НДС которую должен оплатить клиент за оказанную услугу. обычный инпут

            'formula_tariff' => Schema::TYPE_TEXT . ' NULL DEFAULT "" comment "Formula for tariff"', // Формула для вычисления тарифа

            'status' => Schema::TYPE_SMALLINT . '  NULL DEFAULT 0', // Статус, выпадающий писок. Привет значенией : Активен не активен
            'comment' => Schema::TYPE_TEXT . '  NULL',

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',// Это заполняется через бихейвер
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL', // Это заполняется через бихейвер

            'created_at' => Schema::TYPE_INTEGER . ' NULL', // Это заполняется через бихейвер
            'updated_at' => Schema::TYPE_INTEGER . ' NULL', // Это заполняется через бихейвер
            'deleted' => Schema::TYPE_SMALLINT . ' DEFAULT 0', //
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%tl_delivery_proposal_billing_conditions}}');
    }
}
