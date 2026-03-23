<?php

use yii\db\Migration;

/**
 * Class m200626_113927_ecommerce_check_box_delete_old
 */
class m200626_113927_ecommerce_check_box_delete_old extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropColumn('{{%ecommerce_check_box}}', 'inventory_key');
        $this->dropColumn('{{%ecommerce_check_box}}', 'title');

        $this->addColumn('{{%ecommerce_check_box}}', 'inventory_id', $this->integer(11)->defaultValue(0)->comment("")->after('employee_id'));

        $this->dropColumn('{{%ecommerce_check_box_stock}}', 'inventory_key');
        $this->dropColumn('{{%ecommerce_check_box_stock}}', 'title');

        $this->addColumn('{{%ecommerce_check_box_stock}}', 'inventory_id', $this->integer(11)->defaultValue(0)->comment("")->after('id'));



    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m200626_113927_ecommerce_check_box_delete_old cannot be reverted.\n";

        return false;
    }
}
