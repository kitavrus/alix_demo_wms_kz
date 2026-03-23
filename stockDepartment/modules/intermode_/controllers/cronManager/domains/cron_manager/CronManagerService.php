<?php

namespace stockDepartment\modules\intermode\controllers\cronManager\domains\cron_manager;

class CronManagerService
{
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	/**
	 * Есть ли хотя бы одна задача в работе
	 * @return boolean
	 */
	function isExistsAnyWorkingJobs() {
		return CronManager::find()->andWhere(["status"=>CronManagerStatus::WORKING])->exists();
	}

	/**
	 * Получить новую любую задачу
	 * @return CronManager
	 */
	function getAnyOneNewJob() {
		return CronManager::findOne(["status"=>CronManagerStatus::_NEW]);
	}

	/**
	 * Создаем задачу для b2b inbound
	 * @param $orderId integer
	 * @param $orderNumber string
	 * @return CronManager
	 */
	function makeJobB2BInbound($orderId,$orderNumber) {
		return $this->makeJob( "приход B2B ".$orderNumber,$orderId,CronManagerOrderType::B2B_INBOUND);
	}

	/**
	 * Изменить статус задачи на WORKING для b2b inbound
	 * @param $orderId integer
	 * @return CronManager
	 */
	function changeStatusToWorkingB2BInbound($orderId) {
		return $this->changeStatus($orderId,CronManagerOrderType::B2B_INBOUND,CronManagerStatus::WORKING);
	}

	/**
	 * Изменить статус задачи на FINISH для b2b inbound
	 * @param $orderId integer
	 * @return CronManager
	 */
	function changeStatusToFinishB2BInbound($orderId) {
		return $this->changeStatus($orderId,CronManagerOrderType::B2B_INBOUND,CronManagerStatus::FINISH);
	}


	/**
	 * Создаем задачу для b2b outbound
	 * @param $orderId integer
	 * @param $orderNumber string
	 * @return CronManager
	 */
	function makeJobB2BOutbound($orderId,$orderNumber) {
		return $this->makeJob( "Отгрузка B2B ".$orderNumber,$orderId,CronManagerOrderType::B2B_OUTBOUND);
	}

	/**
	 * Изменить статус задачи на WORKING для b2b outbound
	 * @param $orderId integer
	 * @return CronManager
	 */
	function changeStatusToWorkingB2BOutbound($orderId) {
		return $this->changeStatus($orderId,CronManagerOrderType::B2B_OUTBOUND,CronManagerStatus::WORKING);
	}

	/**
	 * Изменить статус задачи на FINISH для b2b outbound
	 * @param $orderId integer
	 * @return CronManager
	 */
	function changeStatusToFinishB2BOutbound($orderId) {
		return $this->changeStatus($orderId,CronManagerOrderType::B2B_OUTBOUND,CronManagerStatus::FINISH);
	}

	/**
	 * Изменить статус задачи
	 * @param $orderId integer
	 * @param $orderType string
	 * @param $jobStatus String
	 * @return CronManager
	 */
	function changeStatus($orderId,$orderType,$jobStatus) {
		$cm = CronManager::findOne(["order_id"=>$orderId,"type"=>$orderType]);
		if ($cm) {
			$cm->status = $jobStatus;
			$cm->save(false);
		}
		return $cm;
	}


	/**
	* Добавляем новый заказ для отправки данных по API
	 * @param $name String
	 * @param $orderId integer
	 * @param $orderType String
	 * @return CronManager
	 */
	function makeJob($name,$orderId,$orderType) {
		$cm = CronManager::findOne(["order_id"=>$orderId,"type"=>$orderType]);
		if (empty($cm)) {
			$cm = new CronManager();
			$cm->name = $name;
			$cm->order_id = $orderId;
			$cm->type = $orderType;
			$cm->status = CronManagerStatus::_NEW;
			$cm->save(false);
		}

		return $cm;
	}
}
