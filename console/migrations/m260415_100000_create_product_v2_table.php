<?php

use yii\db\Migration;

/**
 * Карточка товара v2.
 *
 * Синхронизируется cron-задачей из внешнего источника NMDX
 * (GET /hs/NMDX/items) раз в 30 минут. Идентификатор записи в источнике — `guid`.
 */
class m260415_100000_create_product_v2_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%product_v2}}', [
            'id'                => $this->primaryKey(),
            'guid'              => $this->string(64)->notNull()->comment('GUID товара (PK в источнике NMDX)'),
            'barcode'           => $this->string(32)->null()->comment('Основной штрих-код из мастер-данных'),
            'article'           => $this->string(32)->null()->comment('Артикул'),
            'category'          => $this->string(256)->null()->comment('Категория товара'),
            'name'              => $this->string(256)->null()->comment('Наименование (RU)'),
            'name_kaz'          => $this->string(256)->null()->comment('Наименование (KZ)'),
            'brand'             => $this->string(128)->null()->comment('Бренд'),
            'VAT_rate'          => $this->decimal(5, 2)->null()->comment('Ставка НДС, %'),
            'country_of_origin' => $this->string(128)->null()->comment('Страна происхождения'),
            'description'       => $this->text()->null()->comment('Описание товара'),
            'color_code'        => $this->string(32)->null()->comment('Код цвета (допускает буквенные префиксы: AF501, LF301, P01, G03 и т.п.)'),
            'color_name'        => $this->string(128)->null()->comment('Название цвета'),
            'filling'           => $this->string(64)->null()->comment('Объём / вес (ml/g)'),
            'code_tnved'        => $this->string(32)->null()->comment('Код ТН ВЭД'),
            'code_nkt'          => $this->string(32)->null()->comment('Код НКТ'),
            'created_at'        => $this->integer()->notNull()->comment('Unix timestamp создания'),
            'updated_at'        => $this->integer()->notNull()->comment('Unix timestamp последнего обновления'),
            'deleted'           => $this->smallInteger()->null()->defaultValue(0)->comment('Флаг мягкого удаления (0/1)'),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        $this->createIndex('uq_product_v2_guid', '{{%product_v2}}', 'guid', true);
        $this->createIndex('idx_product_v2_article', '{{%product_v2}}', 'article');
        $this->createIndex('idx_product_v2_barcode', '{{%product_v2}}', 'barcode');
    }

    public function down()
    {
        $this->dropTable('{{%product_v2}}');
    }
}
