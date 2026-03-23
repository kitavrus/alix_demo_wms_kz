<?php

namespace app\modules\intermode\controllers\ecommerce\outbound\domain\constants;

class OutboundSource
{
	const KASPI = "KASPI";
	const CRM = "CRM";


	public static function getKASPI()
	{
		return self::KASPI;
	}

	public static function getCRM()
	{
		return self::CRM;
	}
}