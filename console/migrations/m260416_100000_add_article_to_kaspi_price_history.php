<?php

use yii\db\Migration;

class m260416_100000_add_article_to_kaspi_price_history extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%kaspi_price_history}}', 'article', $this->string(32)->defaultValue(null)->after('product_guid'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%kaspi_price_history}}', 'article');
    }
}
