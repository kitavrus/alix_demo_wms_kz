<?php

use yii\db\Schema;
use yii\db\Migration;

class m141008_090841_alter_field_to_table_client_profile extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `client_profile` DROP FOREIGN KEY `fk_client_profile`");

        $this->execute("ALTER TABLE `client_profile` ADD FOREIGN KEY ( `user_id` ) REFERENCES `wms20`.`client` (
`id`
) ON DELETE RESTRICT ON UPDATE RESTRICT ;");

        $this->execute("ALTER TABLE `wms20`.`client_profile` DROP PRIMARY KEY ,
ADD PRIMARY KEY ( `user_id` )");

        $this->execute("ALTER TABLE `client_profile` ADD INDEX ( `user_id` )");
        $this->execute("ALTER TABLE `client_profile` DROP PRIMARY KEY");
        $this->execute("ALTER TABLE `client_profile` DROP FOREIGN KEY `client_profile_ibfk_1` ;");

    }

    public function down()
    {
        echo "m141008_090841_alter_field_to_table_client_profile cannot be reverted.\n";

        return false;
    }
}
