<?php

use yii\db\Schema;
use yii\db\Migration;

class m140911_102754_create_new_table_tl_delivery_proposal_route_cars extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tl_delivery_proposal_route_cars}}', [
            'id' => Schema::TYPE_PK,

            'route_from' => Schema::TYPE_INTEGER . ' NULL comment "Example: DC-APORT"',// Это поле не редактируется. Только просмотр
            'route_to' => Schema::TYPE_INTEGER . ' NULL comment "Example: DC-APORT"', // Это поле не редактируется. Только просмотр

            'delivery_date' => Schema::TYPE_INTEGER . ' NULL comment "Date of planned delivery"',
            'mc_filled' => Schema::TYPE_DECIMAL . '(26,3) NULL DEFAULT "0" comment "Filled meters cubic"', // Заполненость в метры кубических
            'kg_filled' => Schema::TYPE_DECIMAL . '(26,3) NULL DEFAULT "0" comment "Filled kilograms"', // Заполненость в килограммах
            'agent_id' => Schema::TYPE_INTEGER . ' NULL comment "Agent"', // Выподающий список из [tl_agents]
            'car_id' => Schema::TYPE_INTEGER . ' NULL comment "Agent"', // Выподающий список из [tl_cars]

            'grzch' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT "0" comment "Грзч"', // Горюче смазочные материалы
            'cash_no' => Schema::TYPE_SMALLINT . '  NULL DEFAULT "0" comment "nal/bez"', // наличный и безналичный расчет. выпадающий список
            'price_invoice' => Schema::TYPE_INTEGER . '  NULL comment "Sale for client"', // Цена которую должен оплатить клиент за оказанную услугу. обычный инпут
            'price_invoice_with_vat' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Price invoice with NDS"', // Цена с НДС которую должен оплатить клиент за оказанную услугу. обычный инпут
            'status' => Schema::TYPE_SMALLINT . '  NULL DEFAULT 0', // Статус, выпоадающий писок. Привет значенией : Новый, в пути, доставлен, выполнен. Добавить ввиде констатн в модель.
            'status_invoice' => Schema::TYPE_SMALLINT . '  NULL DEFAULT 0 comment "Invoice status: invoice not set, invoice set, invoice paid"',// Выпадающией список. пример значенией. Счет не выставлен, счен выставлен, Счет оплачен, Добавить ввиде констатн в модель.
            'comment' => Schema::TYPE_TEXT . '  NULL',

            'created_user_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_user_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',

            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%tl_delivery_proposal_route_cars}}');
    }
}