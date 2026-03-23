<?php

use yii\db\Schema;
use yii\db\Migration;

class m140801_024325_add_column_email_to_store_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}','email',Schema::TYPE_STRING . '(64) NOT NULL COMMENT "Store email" AFTER `name`');
    }

    public function down()
    {
        $this->dropColumn('{{%store}}','email');
    }
}
