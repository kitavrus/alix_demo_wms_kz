<?php

use yii\db\Schema;
use yii\db\Migration;

class m140915_042809_update_table_client extends Migration
{
    public function up()
    {
        // user table
        $this->dropIndex('client_confirmation', '{{%client}}');
        $this->dropIndex('client_recovery', '{{%client}}');
        $this->dropColumn('{{%client}}', 'confirmation_token');
        $this->dropColumn('{{%client}}', 'confirmation_sent_at');
        $this->dropColumn('{{%client}}', 'recovery_token');
        $this->dropColumn('{{%client}}', 'recovery_sent_at');
        $this->dropColumn('{{%client}}', 'logged_in_from');
        $this->dropColumn('{{%client}}', 'logged_in_at');
        $this->renameColumn('{{%client}}', 'registered_from', 'registration_ip');
        $this->addColumn('{{%client}}', 'flags', Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0');

        // account table
        $this->renameColumn('{{%client_account}}', 'properties', 'data');
    }

    public function down()
    {
        return false;
    }
}
