<?php

use yii\db\Schema;
use yii\db\Migration;

class m140916_175150_change_created_at_updated_at_car_table extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `tl_cars` CHANGE `created_at` `created_at` INT( 11 ) NULL");
        $this->execute("ALTER TABLE `tl_cars` CHANGE `updated_at` `updated_at` INT( 11 ) NULL");
    }

    public function down()
    {
        echo "m140916_175150_change_created_at_updated_at_car_table cannot be reverted.\n";

        return false;
    }
}
