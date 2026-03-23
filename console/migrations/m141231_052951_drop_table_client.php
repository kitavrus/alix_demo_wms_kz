<?php

use yii\db\Schema;
use yii\db\Migration;

class m141231_052951_drop_table_client extends Migration
{
    public function up()
    {
        $this->dropTable('client_social_account');
        $this->dropTable('client');
    }

    public function down()
    {
        echo "m141231_052951_drop_table_client cannot be reverted.\n";

        return false;
    }
}
