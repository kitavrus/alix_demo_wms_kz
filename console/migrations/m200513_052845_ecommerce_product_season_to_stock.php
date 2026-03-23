<?php

use yii\db\Migration;

/**
 * Class m200513_052845_ecommerce_product_season_to_stock
 */
class m200513_052845_ecommerce_product_season_to_stock extends Migration
{

    public function up()
    {
        $this->addColumn('{{%ecommerce_stock}}', 'product_season', $this->string(16)->defaultValue('')->comment("Product season")->after('product_price'));
        $this->addColumn('{{%ecommerce_stock}}', 'product_season_year', $this->string(4)->defaultValue('')->comment("Product season year")->after('product_season'));
        $this->addColumn('{{%ecommerce_stock}}', 'product_season_full', $this->string(32)->defaultValue('')->comment("Product season full")->after('product_season_year'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_stock}}', 'product_season');
        $this->dropColumn('{{%ecommerce_stock}}', 'product_season_year');
        $this->dropColumn('{{%ecommerce_stock}}', 'product_season_full');
        return false;
    }
}
