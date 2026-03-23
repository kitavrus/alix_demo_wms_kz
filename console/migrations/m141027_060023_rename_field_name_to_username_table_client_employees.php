<?php

use yii\db\Schema;
use yii\db\Migration;

class m141027_060023_rename_field_name_to_username_table_client_employees extends Migration
{
    public function up()
    {
        $this->renameColumn('client_employees','name','username');
    }

    public function down()
    {
        $this->renameColumn('client_employees','username','name');
    }
}
