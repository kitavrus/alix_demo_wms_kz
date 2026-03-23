<?php

use yii\db\Migration;

/**
 * Class m210930_082625_add_api_send_data_outbound_order
 */
class m210930_082625_add_api_send_data_outbound_order extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function safeUp()
	{
		$this->addColumn('{{%outbound_orders}}', 'api_send_data', $this->text()->defaultValue('')->comment("Данне которые отправляем по апи")->after('fail_delivery_status'));
	}

	/**
	 * @inheritdoc
	 */
	public function safeDown()
	{
		echo "m210930_082625_add_api_send_data_outbound_order cannot be reverted.\n";

		return false;
	}
}
