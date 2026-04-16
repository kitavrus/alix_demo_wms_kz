<?php

use yii\db\Migration;

/**
 * Историческая таблица остатков товаров для Kaspi.
 *
 * Kaspi не имеет REST API для обновления остатков — они подаются через xlsx-прайс.
 * Таблица хранит override-значения qty по product_guid с датой активации.
 * PriceListService подставляет активный override в колонки PP1–PP5 вместо COUNT(*)
 * по ecommerce_stock.
 */
class m260414_120000_create_kaspi_stock_history_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%kaspi_stock_history}}', [
            'id'                  => $this->primaryKey(),
            'product_guid'        => $this->string(128)->notNull()->comment('GUID товара на Kaspi (merchantProductCode / SKU)'),
            'qty'                 => $this->integer()->notNull()->comment('Количество на складе (override для PP1)'),
            'note'                => $this->text()->null()->comment('Произвольная заметка / комментарий к изменению остатка'),
            'effective_from'      => $this->integer()->notNull()->comment('Дата с которой остаток становится актуальным (Unix timestamp)'),
            'push_status'         => $this->string(16)->notNull()->defaultValue('PENDING')->comment('Статус: PENDING, SENT, ERROR, SKIPPED'),
            'push_response'       => $this->text()->null()->comment('Сырой ответ при генерации/отправке'),
            'push_at'             => $this->integer()->null()->comment('Unix timestamp момента активации'),
            'created_at'          => $this->integer()->notNull()->comment('Unix timestamp создания записи'),
            'created_user_id'     => $this->integer()->null()->comment('ID пользователя, создавшего запись'),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        $this->createIndex('idx_kaspi_stock_history_product_guid', '{{%kaspi_stock_history}}', 'product_guid');
        $this->createIndex('idx_kaspi_stock_history_effective_from', '{{%kaspi_stock_history}}', 'effective_from');
        $this->createIndex('idx_kaspi_stock_history_push_status', '{{%kaspi_stock_history}}', 'push_status');
    }

    public function down()
    {
        $this->dropTable('{{%kaspi_stock_history}}');
    }
}
