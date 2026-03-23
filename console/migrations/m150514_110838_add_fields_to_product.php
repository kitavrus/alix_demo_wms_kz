<?php

use yii\db\Schema;
use yii\db\Migration;

class m150514_110838_add_fields_to_product extends Migration
{
    public function up()
    {
        $this->addColumn('{{%product}}','model', Schema::TYPE_STRING . '(32) NULL AFTER `name`');
        $this->addColumn('{{%product}}','color', Schema::TYPE_STRING . '(32) NULL AFTER `model`');
        $this->addColumn('{{%product}}','size', Schema::TYPE_STRING . '(32) NULL AFTER `color`');
        $this->addColumn('{{%product}}','season', Schema::TYPE_STRING . '(32) NULL AFTER `size`');
        $this->addColumn('{{%product}}','made_in', Schema::TYPE_STRING . '(32) NULL AFTER `season`');
        $this->addColumn('{{%product}}','composition', Schema::TYPE_STRING . '(32) NULL AFTER `made_in`');
        $this->addColumn('{{%product}}','category', Schema::TYPE_STRING . '(32) NULL AFTER `composition`');
        $this->addColumn('{{%product}}','gender', Schema::TYPE_STRING . '(32) NULL AFTER `category`');

        $this->renameColumn('{{%product}}', 'modified_user_id', 'updated_user_id');
        $this->renameColumn('{{%product_barcodes}}', 'modified_user_id', 'updated_user_id');
    }

    public function down()
    {
        $this->dropColumn('{{%product}}','model');
        $this->dropColumn('{{%product}}','color');
        $this->dropColumn('{{%product}}','size');
        $this->dropColumn('{{%product}}','season');
        $this->dropColumn('{{%product}}','made_in');
        $this->dropColumn('{{%product}}','composition');
        $this->dropColumn('{{%product}}','category');
        $this->dropColumn('{{%product}}','gender');
    }

}
