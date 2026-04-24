<?php

use yii\db\Migration;

/**
 * Добавляет в ecommerce_inbound / ecommerce_inbound_items поля, необходимые
 * для переноса Alix B2B inbound (POST alix/ecommerce/api/v1/inbound/orders и
 * web-сканирование alix/ecommerce/inbound/scanning/*) с InboundOrder/InboundOrderItem.
 *
 * supplier_id сохранён для обратной совместимости с legacy inbound_orders.
 *
 * Миграция идемпотентна — часть колонок (product_name, product_brand, product_color,
 * product_model) могла уже существовать в ecommerce_inbound_items до этой миграции,
 * а также миграция могла частично примениться и упасть посередине.
 */
class m260424_140000_add_b2b_fields_to_ecommerce_inbound extends Migration
{
    public function up()
    {
        $this->addColumnIfMissing('{{%ecommerce_inbound}}', 'client_order_id', $this->string(64)->null()->comment('UUID документа на стороне 1С'));
        $this->addColumnIfMissing('{{%ecommerce_inbound}}', 'from_point_id', $this->integer()->null()->comment('Точка отгрузки (Store.id)'));
        $this->addColumnIfMissing('{{%ecommerce_inbound}}', 'supplier_id', $this->integer()->null()->comment('Поставщик (для обратной совместимости)'));
        $this->addColumnIfMissing('{{%ecommerce_inbound}}', 'comments', $this->text()->null());

        if (!$this->indexExists('{{%ecommerce_inbound}}', 'idx_ecommerce_inbound_client_order_id')) {
            $this->createIndex('idx_ecommerce_inbound_client_order_id', '{{%ecommerce_inbound}}', 'client_order_id');
        }

        $this->addColumnIfMissing('{{%ecommerce_inbound_items}}', 'product_sku', $this->string(128)->null());
        $this->addColumnIfMissing('{{%ecommerce_inbound_items}}', 'product_name', $this->string(128)->null());
        $this->addColumnIfMissing('{{%ecommerce_inbound_items}}', 'product_model', $this->string(128)->null());
        $this->addColumnIfMissing('{{%ecommerce_inbound_items}}', 'product_brand', $this->string(1024)->null());
        $this->addColumnIfMissing('{{%ecommerce_inbound_items}}', 'product_color', $this->string(128)->null());
        $this->addColumnIfMissing('{{%ecommerce_inbound_items}}', 'begin_datetime', $this->integer()->null());
        $this->addColumnIfMissing('{{%ecommerce_inbound_items}}', 'end_datetime', $this->integer()->null());
    }

    public function down()
    {
        $this->dropColumnIfExists('{{%ecommerce_inbound_items}}', 'end_datetime');
        $this->dropColumnIfExists('{{%ecommerce_inbound_items}}', 'begin_datetime');
        $this->dropColumnIfExists('{{%ecommerce_inbound_items}}', 'product_color');
        $this->dropColumnIfExists('{{%ecommerce_inbound_items}}', 'product_brand');
        $this->dropColumnIfExists('{{%ecommerce_inbound_items}}', 'product_model');
        $this->dropColumnIfExists('{{%ecommerce_inbound_items}}', 'product_name');
        $this->dropColumnIfExists('{{%ecommerce_inbound_items}}', 'product_sku');

        if ($this->indexExists('{{%ecommerce_inbound}}', 'idx_ecommerce_inbound_client_order_id')) {
            $this->dropIndex('idx_ecommerce_inbound_client_order_id', '{{%ecommerce_inbound}}');
        }

        $this->dropColumnIfExists('{{%ecommerce_inbound}}', 'comments');
        $this->dropColumnIfExists('{{%ecommerce_inbound}}', 'supplier_id');
        $this->dropColumnIfExists('{{%ecommerce_inbound}}', 'from_point_id');
        $this->dropColumnIfExists('{{%ecommerce_inbound}}', 'client_order_id');

        return true;
    }

    private function addColumnIfMissing($table, $column, $type)
    {
        if ($this->columnExists($table, $column)) {
            echo "    > column $column on $table already exists, skipped\n";
            return;
        }
        $this->addColumn($table, $column, $type);
    }

    private function dropColumnIfExists($table, $column)
    {
        if (!$this->columnExists($table, $column)) {
            return;
        }
        $this->dropColumn($table, $column);
    }

    private function columnExists($table, $column)
    {
        $schema = $this->db->getTableSchema($table, true);
        return $schema !== null && isset($schema->columns[$column]);
    }

    private function indexExists($table, $indexName)
    {
        $rawTable = $this->db->quoteSql($table);
        $rows = $this->db->createCommand("SHOW INDEX FROM {$rawTable} WHERE Key_name = :n", [':n' => $indexName])->queryAll();
        return !empty($rows);
    }
}
