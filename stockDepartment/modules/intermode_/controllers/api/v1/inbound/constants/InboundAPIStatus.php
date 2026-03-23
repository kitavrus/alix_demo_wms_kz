<?php

namespace app\modules\intermode\controllers\api\v1\inbound\constants;

class InboundAPIStatus
{
	const _NEW = "new";// "Новый";
	const IN_WORK = "in_work";// "ВРаботе";
	const COMPLETED = "completed";// "Принят";
	const COMPLETED_WITH_DIFFERENCE = "completed_with_difference";// "ПринятСОтклонениями";
}