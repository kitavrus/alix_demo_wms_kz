<?php

use yii\db\Schema;
use yii\db\Migration;

class m150121_043333_change_field_created_user_id_modified_user_id_table_codebook extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `codebook` CHANGE `created_user_id` `created_user_id` INT( 11 ) NULL");
        $this->execute("ALTER TABLE `codebook` CHANGE `modified_user_id` `updated_user_id` INT( 11 ) NULL");
    }

    public function down()
    {
        echo "m150121_043333_change_field_created_user_id_modified_user_id_table_codebook cannot be reverted.\n";

        return false;
    }
}
