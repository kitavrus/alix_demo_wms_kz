<?php

use yii\db\Migration;

/**
 * Class m220523_193456_add_field_api_complete_status
 */
class m220523_193456_add_field_api_complete_status extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
		$this->addColumn('{{%outbound_orders}}', 'api_complete_status', $this->string()->defaultValue('no')->comment("Накладная закрыта по апи")->after('api_send_data'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m220523_193456_add_field_api_complete_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220523_193456_add_field_api_complete_status cannot be reverted.\n";

        return false;
    }
    */
}
