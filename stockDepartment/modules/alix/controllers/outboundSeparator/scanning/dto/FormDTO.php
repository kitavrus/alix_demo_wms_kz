<?php
namespace stockDepartment\modules\alix\controllers\outboundSeparator\scanning\dto;

class FormDTO
{
	public $id;
	public $out_box_barcode;
	public $in_box_barcode;
	public $product_barcode;

	public function __construct($id,$in_box_barcode,$out_box_barcode,$product_barcode)
	{
		$this->id = $id;
		$this->out_box_barcode = $out_box_barcode;
		$this->in_box_barcode = $in_box_barcode;
		$this->product_barcode = $product_barcode;
	}
}