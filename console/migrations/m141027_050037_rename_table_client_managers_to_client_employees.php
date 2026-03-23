<?php

use yii\db\Schema;
use yii\db\Migration;

class m141027_050037_rename_table_client_managers_to_client_employees extends Migration
{
    public function up()
    {
        $this->renameTable('client_managers','client_employees');
    }

    public function down()
    {
        $this->renameTable('client_employees','client_managers');
    }
}
