<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m190715_060920_ecommerce_outbound
 */
class m190715_060920_ecommerce_outbound extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_outbound', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer(11)->defaultValue(0)->comment("Client id"),
            'responsible_delivery_id' => $this->integer()->defaultValue(0)->comment("Ответственный за доставку"),

            'order_number' => $this->string(36)->defaultValue('')->comment("Order number"),
            'external_order_number' => $this->string(36)->defaultValue('')->comment("External Order number"),

            'expected_qty' => $this->integer(11)->defaultValue(0)->comment("Expected qty"),
            'allocated_qty' => $this->integer(11)->defaultValue(0)->comment("Allocated qty"),
            'accepted_qty' => $this->integer(11)->defaultValue(0)->comment("Accepted qty"),

            'place_expected_qty' => $this->integer(11)->defaultValue(0)->comment("Place expected qty"),
            'place_accepted_qty' => $this->integer(11)->defaultValue(0)->comment("Place accepted qty"),

            'mc' => $this->decimal(26,3)->defaultValue(0)->comment("Mc"),
            'kg' => $this->decimal(26,3)->defaultValue(0)->comment("Kg"),

            'status' => $this->smallInteger()->defaultValue(0)->comment("Status"),

            'first_name' => $this->string(128)->defaultValue('')->comment("first_name"),
            'middle_name' => $this->string(128)->defaultValue('')->comment("middle_name"),
            'last_name' => $this->string(128)->defaultValue('')->comment("last_name"),
            'customer_name' => $this->string(256)->defaultValue('')->comment("Customer full name"),
            'phone_mobile1' => $this->string(128)->defaultValue('')->comment("Phone mobile 1"),
            'phone_mobile2' => $this->string(128)->defaultValue('')->comment("Phone mobile 2"),
            'email' => $this->string(128)->defaultValue('')->comment("email"),

            'country' => $this->string(128)->defaultValue('')->comment("country"),
            'region' => $this->string(128)->defaultValue('')->comment("region"),
            'city' => $this->string(128)->defaultValue('')->comment("city"),
            'zip_code' => $this->string(128)->defaultValue('')->comment("zip_code"),
            'street' => $this->string(128)->defaultValue('')->comment("street"),
            'house' => $this->string(6)->defaultValue('')->comment("house"),
            'building' => $this->string(6)->defaultValue('')->comment("Корпус"),
            'entrance' => $this->string(6)->defaultValue('')->comment("Подъезд"),
            'flat' => $this->string(6)->defaultValue('')->comment("Номер квартиры"),
            'intercom' => $this->string(6)->defaultValue('')->comment("Домофон"),
            'floor' => $this->string(6)->defaultValue('')->comment("Этаж"),
            'elevator' => $this->smallInteger(1)->defaultValue(0)->comment("Лифт"),
            'customer_address' => $this->string(512)->defaultValue('')->comment("Адрес"),

            'customer_comment' => $this->text()->defaultValue('')->comment("Комментарий покупателя"),
            'ttn' => $this->text()->defaultValue('')->comment("Номер транспортной накладной"),
            'payment_method' => $this->smallInteger(6)->defaultValue(0)->comment("Метод оплаты"),
            'payment_status' => $this->smallInteger(6)->defaultValue(0)->comment("Статус оплаты"),

            'data_created_on_client' =>$this->integer(11)->defaultValue(null)->comment("Data created on client"),
            'print_picking_list_date' =>$this->integer(11)->defaultValue(null)->comment("Print picking list date"),
            'begin_datetime' =>$this->integer(11)->defaultValue(null)->comment("Begin scanning datetime"),
            'end_datetime' =>$this->integer(11)->defaultValue(null)->comment("End scanning datetime"),
            'packing_date' =>$this->integer(11)->defaultValue(null)->comment("Packing date"),
            'date_left_warehouse' =>$this->integer(11)->defaultValue(null)->comment("Date left warehouse"),
            'date_delivered_to_customer' =>$this->integer(11)->defaultValue(null)->comment("Date delivered to customer"),

            'client_Priority' =>$this->smallInteger(6)->defaultValue(0)->comment("Client Priority"),
            'client_CargoCompany' =>$this->string(256)->defaultValue('')->comment("Client CargoCompany"),
            'client_ShippingCountryCode' =>$this->string(24)->defaultValue('')->comment("Client Shipping Country Code"),
            'client_ShippingCity' =>$this->string(64)->defaultValue('')->comment("Client Shipping City"),
            'client_PackMessage' =>$this->text()->defaultValue('')->comment("Client Pack Message"),
            'client_GiftWrappingMessage' =>$this->text()->defaultValue('')->comment("Client Gift Wrapping Message"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_outbound}}');
    }
}
