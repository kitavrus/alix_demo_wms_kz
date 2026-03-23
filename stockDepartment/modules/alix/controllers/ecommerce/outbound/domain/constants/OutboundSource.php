<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\constants;

class OutboundSource
{
	const KASPI = "KASPI";
	const CRM = "CRM";
	const LAMODA = "LAMODA";
	
	public static function getLAMODA()
	{
		return self::LAMODA;
	}

	public static function getKASPI()
	{
		return self::KASPI;
	}

	public static function getCRM()
	{
		return self::CRM;
	}
}