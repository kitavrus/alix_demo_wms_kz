<?php

use yii\db\Schema;
use yii\db\Migration;

class m140910_120946_create_new_table_tl_delivery_routes extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tl_delivery_routes}}', [
            'id' => Schema::TYPE_PK,
            'client_id' => Schema::TYPE_INTEGER . ' NULL comment "Example: DeTacty. Internal client id"', // Тут выбадающий список и клиентов
            'tl_delivery_proposal_id' => Schema::TYPE_INTEGER . ' NULL comment ""', // Ставится автоматом
            'route_from' => Schema::TYPE_INTEGER . '  NULL comment "Example: DC-APORT"', // Тут выпадающий список из доступных путей для этого клиента и возможность добавить новый пунк доставки
            'route_to' => Schema::TYPE_INTEGER . '  NULL comment "Example: DC-APORT"', // Тут выпадающий список из доступных путей для этого клиента и возможность добавить новый пунк доставки
            'delivery_date' => Schema::TYPE_INTEGER . '  NULL comment "Date of planned delivery"', // Дата желаемой доставки
            'mc' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Meters cubic"',// Метры кубические. обычный инпут
            'mc_actual' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT "0" comment "Meters cubic"',//Это поле инпут. Оно заблакировано. Его менять клиент не может. Это фактическое количество метров кубичестких отгруженных в пункт назначения
            'kg' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0" comment "Kilogram"', // Киллограмы. обычный инпут
            'kg_actual' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT "0" comment "Kilogram real"', //Это поле инпут. Оно заблакировано. Его менять клиент не может. Фактическое кол-во доставленных килограммов
            'number_places' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0" comment "Estimated number palaces"', // Перполагаемое количество мест. обычный инпут
            'number_places_actual' => Schema::TYPE_INTEGER . '  NULL DEFAULT "0" comment "Real number palaces"', //Это поле инпут. Оно заблакировано. Его менять клиент не может. Фактическое количество мест.


            'cash_no' => Schema::TYPE_SMALLINT . '  NULL DEFAULT "0" comment "nal/bez"', // наличный и безналичный расчет. выпадающий список
            'price_invoice' => Schema::TYPE_INTEGER . '  NULL comment "Sale for client"', // Цена которую должен оплатить клиент за оказанную услугу. обычный инпут
            'price_invoice_with_vat' => Schema::TYPE_DECIMAL . '(26,3)  NULL DEFAULT "0" comment "Price invoice with NDS"', // Цена с НДС которую должен оплатить клиент за оказанную услугу. обычный инпут
            'status' => Schema::TYPE_SMALLINT . '  NULL DEFAULT 0', // Статус, выпоадающий писок. Привет значенией : Новый, в пути, доставлен, выполнен. Добавить ввиде констатн в модель.
            'status_invoice' => Schema::TYPE_SMALLINT . '  NULL DEFAULT 0 comment "Invoice status: invoice not set, invoice set, invoice paid"',// Выпадающией список. пример значенией. Счет не выставлен, счен выставлен, Счет оплачен, Добавить ввиде констатн в модель.
            'comment' => Schema::TYPE_TEXT . '  NULL',

            'created_user_id' =>  Schema::TYPE_INTEGER . '  NULL',// Это заполняется через бихейвер
            'updated_user_id' =>  Schema::TYPE_INTEGER . '  NULL', // Это заполняется через бихейвер

            'created_at' => Schema::TYPE_INTEGER . ' NULL', // Это заполняется через бихейвер
            'updated_at' => Schema::TYPE_INTEGER . ' NULL', // Это заполняется через бихейвер
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%tl_delivery_routes}}');
    }
}
