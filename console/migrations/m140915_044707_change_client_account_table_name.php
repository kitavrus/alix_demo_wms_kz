<?php

use yii\db\Schema;
use yii\db\Migration;

class m140915_044707_change_client_account_table_name extends Migration
{
    public function up()
    {
        $this->renameTable('{{%client_account}}', '{{%client_social_account}}');
    }

    public function down()
    {
        $this->renameTable('{{%client_social_account}}', '{{%client_account}}');
    }
}
