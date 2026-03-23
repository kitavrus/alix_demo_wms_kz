<?php

use yii\db\Schema;
use yii\db\Migration;

class m141015_041700_alter_field_created_updated_at_to_tlagents extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `tl_agents` CHANGE `updated_at` `updated_at` INT( 11 ) NULL");
        $this->execute("ALTER TABLE `tl_agents` CHANGE `created_at` `created_at` INT( 11 ) NULL");
    }

    public function down()
    {
        echo "m141015_041700_alter_field_created_updated_at_to_tlagents cannot be reverted.\n";

        return false;
    }
}
