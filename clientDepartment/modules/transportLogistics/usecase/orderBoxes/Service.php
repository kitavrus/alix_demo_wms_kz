<?php

namespace clientDepartment\modules\transportLogistics\usecase\orderBoxes;

use common\modules\transportLogistics\models\TlDeliveryProposalOrderBoxes;

class Service
{
	public function addBox($dpID,$boxBarcode,$employeeName) {
		$box = new TlDeliveryProposalOrderBoxes();
		$box->tl_delivery_proposal_id = $dpID;
		$box->box_barcode = $boxBarcode;
		$box->employee_name = $employeeName;
		$box->save(false);
	}
	public function getBoxes($dpID) {
		return TlDeliveryProposalOrderBoxes::find()->select("box_barcode")
										   ->andWhere(["tl_delivery_proposal_id"=>$dpID])
										   ->asArray()
			->orderBy(["id"=>SORT_DESC])
										   ->all();
	}
	public function getBoxQty($dpID) {
		return TlDeliveryProposalOrderBoxes::find()
										   ->andWhere(["tl_delivery_proposal_id"=>$dpID])
										   ->count();
	}

	public function isBoxExists($boxBarcode) {
		return TlDeliveryProposalOrderBoxes::find()
										   ->andWhere(["box_barcode"=>$boxBarcode])
										   ->exists();
	}
}