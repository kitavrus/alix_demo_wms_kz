<?php

use yii\db\Schema;
use yii\db\Migration;

class m140915_044353_fix_ip_field_client extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%client}}', 'registration_ip', Schema::TYPE_INTEGER . ' UNSIGNED');
    }

    public function down()
    {
        $this->alterColumn('{{%client}}', 'registration_ip', Schema::TYPE_INTEGER);
    }
}
