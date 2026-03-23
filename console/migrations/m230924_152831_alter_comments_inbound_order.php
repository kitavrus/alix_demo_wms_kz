<?php

use yii\db\Migration;

/**
 * Class m230924_152831_alter_comments_inbound_order
 */
class m230924_152831_alter_comments_inbound_order extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
		$this->execute("ALTER TABLE `inbound_orders`
CHANGE `comments` `comments` text COLLATE 'utf8_general_ci' NULL DEFAULT '' COMMENT 'Comments' AFTER `data_created_on_client`");

    }

    public function down()
    {
        echo "m230924_152831_alter_comments_inbound_order cannot be reverted.\n";

        return false;
    }

}
