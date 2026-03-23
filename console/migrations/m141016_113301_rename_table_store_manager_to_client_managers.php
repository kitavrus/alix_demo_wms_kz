<?php

use yii\db\Schema;
use yii\db\Migration;

class m141016_113301_rename_table_store_manager_to_client_managers extends Migration
{
    public function up()
    {
        $this->renameTable('store_manager','client_managers');
    }

    public function down()
    {
        $this->renameTable('client_managers','store_manager');
    }
}
