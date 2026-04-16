<?php

use yii\db\Migration;

/**
 * Штрих-коды карточки товара v2.
 *
 * Один товар (product_v2) может иметь несколько штрих-кодов.
 * Таблица пополняется тем же cron-процессом, что и product_v2.
 */
class m260415_100100_create_product_barcodes_v2_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%product_barcodes_v2}}', [
            'id'         => $this->primaryKey(),
            'product_id' => $this->integer()->notNull()->comment('FK -> product_v2.id'),
            'barcode'    => $this->string(32)->notNull()->comment('Штрих-код товара'),
            'created_at' => $this->integer()->notNull()->comment('Unix timestamp создания'),
            'updated_at' => $this->integer()->notNull()->comment('Unix timestamp последнего обновления'),
            'deleted'    => $this->smallInteger()->null()->defaultValue(0)->comment('Флаг мягкого удаления (0/1)'),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        $this->createIndex(
            'uq_product_barcodes_v2_product_barcode',
            '{{%product_barcodes_v2}}',
            ['product_id', 'barcode'],
            true
        );
        $this->createIndex('idx_product_barcodes_v2_barcode', '{{%product_barcodes_v2}}', 'barcode');

        $this->addForeignKey(
            'fk_product_barcodes_v2_product',
            '{{%product_barcodes_v2}}',
            'product_id',
            '{{%product_v2}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_product_barcodes_v2_product', '{{%product_barcodes_v2}}');
        $this->dropTable('{{%product_barcodes_v2}}');
    }
}
