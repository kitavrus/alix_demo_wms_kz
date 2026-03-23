<?php

use yii\db\Schema;
use yii\db\Migration;

class m141016_060804_add_field_user_type_to_table_user extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}','user_type',Schema::TYPE_SMALLINT . '(4) NULL COMMENT "User type: operator, client, shop owner, etc " AFTER `id`');
    }

    public function down()
    {
        $this->dropColumn('{{%user}}','user_type');
    }
}
