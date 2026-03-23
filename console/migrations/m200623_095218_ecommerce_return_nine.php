<?php

use yii\db\Migration;

/**
 * Class m200623_095218_ecommerce_return_nine
 */
class m200623_095218_ecommerce_return_nine extends Migration
{
    public function up()
    {
        $this->addColumn('{{%ecommerce_return}}', 'outbound_box', $this->string(18)->defaultValue('')->comment("")->after('status'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_return}}', 'outbound_box');
        return false;
    }
}
