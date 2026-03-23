<?php

use yii\db\Schema;
use yii\db\Migration;

class m141021_130011_add_field_update_create_to_table_store extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}','created_user_id',Schema::TYPE_INTEGER . '(11) NULL COMMENT "Created user id"');
        $this->addColumn('{{%store}}','updated_user_id',Schema::TYPE_INTEGER . '(11) NULL COMMENT "Updated user id"');
    }

    public function down()
    {
        $this->dropColumn('{{%store}}','created_user_id');
        $this->dropColumn('{{%store}}','updated_user_id');
    }
}
