<?php

namespace app\modules\intermode\controllers\outbound\domain;

use common\modules\outbound\models\OutboundOrder;

class OutboundRepository
{
	/**
	 *
	 */
	public function getClientID()
	{
		return 103;
	}

	/**
	 * @return array|OutboundOrder|\yii\db\ActiveRecord
	 */
	public function getOrder($id)
	{
		return OutboundOrder::find()->andWhere([
			"id" => $id,
			"client_id" => $this->getClientID(),
		])->one();
	}
}