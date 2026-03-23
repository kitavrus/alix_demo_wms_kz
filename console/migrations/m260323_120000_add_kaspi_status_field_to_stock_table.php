<?php

use yii\db\Migration;

class m260323_120000_add_kaspi_status_field_to_stock_table extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%stock}}',
            'kaspi_stock_status',
            $this->string(32)->defaultValue('')->comment('Kaspi stock sync status')->after('system_status_description')
        );
        $this->addColumn(
            '{{%stock}}',
            'kaspi_order_status',
            $this->string(32)->defaultValue('')->comment('Kaspi order status')->after('kaspi_stock_status')
        );
    }

    public function down()
    {
        $this->dropColumn('{{%stock}}', 'kaspi_order_status');
        $this->dropColumn('{{%stock}}', 'kaspi_stock_status');
        return false;
    }
}

