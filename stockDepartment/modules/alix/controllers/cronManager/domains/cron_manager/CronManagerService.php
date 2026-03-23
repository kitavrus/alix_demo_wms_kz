<?php

namespace stockDepartment\modules\alix\controllers\cronManager\domains\cron_manager;

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
	 *
	 * @return array|CronManager|\yii\db\ActiveRecord
	 */
	function getAnyOneNewJob() {
		return CronManager::find()
						  ->andWhere(["status"=>CronManagerStatus::_NEW])
						  ->andWhere("total_counter > 60")
						  // ->andWhere("id=86")
						  ->one();
	}

	/**
	 * Крон джобу запускаем каждую минуту и увеличиваем счетчик на 1 или любое значение
	 * @return int
	 */
	function increasingCounterForJob() {
		return CronManager::updateAllCounters(["total_counter"=>1],["status"=>CronManagerStatus::_NEW]);
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
	 * @param $resultMessage string
	 * @return CronManager
	 */
	function changeStatusToFinishB2BInbound($orderId,$resultMessage = "") {
		return $this->changeStatus($orderId,CronManagerOrderType::B2B_INBOUND,CronManagerStatus::FINISH,$resultMessage);
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
	 * @param $resultMessage string
	 * @return CronManager
	 */
	function changeStatusToFinishB2BOutbound($orderId,$resultMessage = "") {
		return $this->changeStatus($orderId,CronManagerOrderType::B2B_OUTBOUND,CronManagerStatus::FINISH,$resultMessage);
	}

	/**
	 * Изменить статус задачи
	 * @param $orderId integer
	 * @param $orderType string
	 * @param $jobStatus String
	 * @return CronManager
	 */
	function changeStatus($orderId,$orderType,$jobStatus,$resultMessage = "") {
		$cm = CronManager::findOne(["order_id"=>$orderId,"type"=>$orderType]);
		if ($cm) {
			$cm->status = $jobStatus;
			$cm->result_message = $resultMessage;
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

	function checkAndResetWorkingJobs()
	{
		echo "checkAndResetWorkingJobs start\n";

		$hour = 3600;
		$now = time();
		$maxTotalCounter = 70;

		$workingJobs = CronManager::find()
			->andWhere(['status' => CronManagerStatus::WORKING])
			->orderBy(['id' => SORT_ASC])
			->all();

		foreach ($workingJobs as $job) {
			echo "WORKING Job ID: " . $job->id . " (Order: " . $job->order_id . ", Type: " . $job->type . ")\n";

			$shouldReset = false;

			// Ищем NEW джобы с total_counter > 70
			$problematicNewJobs = CronManager::find()
				->andWhere([
					'status' => CronManagerStatus::_NEW,
					'order_id' => $job->order_id,
					'type' => $job->type
				])
				->andWhere(['>', 'total_counter', $maxTotalCounter])
				->count();

			if ($problematicNewJobs > 0) {
				$shouldReset = true;
			}

			// Если WORKING джоба слишком старая
			if (!$shouldReset) {
				$createdAt = $job->created_at;
				$jobAge = $now - $createdAt;

				if ($jobAge > $hour) {
					$shouldReset = true;
				}
			}

			if ($shouldReset) {
				$job->status = CronManagerStatus::_NEW;
				$job->save();
				echo "RESET Job ID: " . $job->id . "\n";
			}
		}

		echo "checkAndResetWorkingJobs completed\n";
	}
}