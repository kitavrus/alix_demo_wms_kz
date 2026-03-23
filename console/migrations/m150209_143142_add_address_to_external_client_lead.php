<?php

use yii\db\Schema;
use yii\db\Migration;

class m150209_143142_add_address_to_external_client_lead extends Migration
{
    public function up()
    {
        $this->addColumn('{{%external_client_lead}}','client_address',Schema::TYPE_INTEGER . ' NULL AFTER `client_name`');
    }

    public function down()
    {
        $this->dropColumn('{{%external_client_lead}}','client_address');
    }
}
