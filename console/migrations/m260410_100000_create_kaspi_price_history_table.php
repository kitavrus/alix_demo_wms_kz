<?php

use yii\db\Migration;

/**
 * Историческая таблица цен товаров для Kaspi.
 *
 * Каждая запись — одно событие изменения цены по конкретному GUID товара.
 * Позволяет видеть историю: какая цена была в другое время
 * При наступлении даты effective_from цена активируется — отправляется в Kaspi API.
 */
class m260410_100000_create_kaspi_price_history_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%kaspi_price_history}}', [
            'id'                  => $this->primaryKey(),
            'product_guid'        => $this->string(128)->notNull()->comment('GUID товара на Kaspi (merchantProductCode / SKU)'),
            'price'               => $this->decimal(12, 2)->notNull()->comment('Цена в KZT'),
            'price_type'          => $this->string(64)->notNull()->defaultValue('BASE')->comment('Тип цены (BASE, SALE, PROMO и т.п.)'),
            'note'                => $this->text()->null()->comment('Произвольная заметка / комментарий к изменению цены'),
            'effective_from'      => $this->integer()->notNull()->comment('Дата с которой цена становится актуальной (Unix timestamp)'),
            'push_status'         => $this->string(16)->notNull()->defaultValue('PENDING')->comment('Статус отправки в Kaspi: PENDING, SENT, ERROR, SKIPPED'),
            'push_response'       => $this->text()->null()->comment('Сырой JSON-ответ от Kaspi API при отправке'),
            'push_at'             => $this->integer()->null()->comment('Unix timestamp момента отправки в Kaspi'),
            'created_at'          => $this->integer()->notNull()->comment('Unix timestamp создания записи'),
            'created_user_id'     => $this->integer()->null()->comment('ID пользователя, создавшего запись'),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        $this->createIndex('idx_kaspi_price_history_product_guid', '{{%kaspi_price_history}}', 'product_guid');
        $this->createIndex('idx_kaspi_price_history_effective_from', '{{%kaspi_price_history}}', 'effective_from');
        $this->createIndex('idx_kaspi_price_history_push_status', '{{%kaspi_price_history}}', 'push_status');
    }

    public function down()
    {
        $this->dropTable('{{%kaspi_price_history}}');
    }
}
