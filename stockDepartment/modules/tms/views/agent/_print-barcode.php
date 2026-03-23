<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 24.02.15
 * Time: 10:15
 */

use common\modules\client\models\ClientEmployees;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\outbound\models\OutboundBoxLabels;
use yii\helpers\BaseFileHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $codeBookModel common\modules\codebook\models\Codebook */

////Yii::$app->get('tcpdf');;;
$pdf = new TCPDF( 'P', 'mm', 'A4', true, 'UTF-8');
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('nmdx.com');
$pdf->SetTitle('Product labels');
$pdf->SetSubject('Product labels');
$pdf->SetKeywords('nmdx.com, product, label');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//set margins
$pdf->SetMargins(2, 2, 2, true);

//set auto page breaks
$pdf->SetAutoPageBreak(false, 0);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

$pdf->AddPage('L', 'NOMADEX70X100', true);


$style = array(
	'border'=>false,
	'padding'=>0,
	'hpadding'=>0,
	'vpadding'=>0.5,
	'fgcolor'=>array(0, 0, 0),
	'bgcolor'=>false,
	'text'=>true,//Текст снизу
	'font'=>'dejavusans',
	'fontsize'=>10,//Размер шрифта
	'stretchtext'=>4,//Растягивание
	'stretch'=>true,
	'fitwidth'=>true,
	'cellfitalign'=>'',
	'position'=>'C',
	'align'=>'C',
);
$agentBarcode = sprintf("%014d",$model->id);

$pdf->write1DBarcode($agentBarcode, 'C128', 0, 0, '70', 70, 1.5, $style, 'C');

$pdf->lastPage();
$dirPath = 'uploads/agent-barcodes/'.date('Ymd').'/'.date('His');
$fileName = time() . '-agent-barcode.pdf';
BaseFileHelper::createDirectory($dirPath);
$fullPath = $dirPath.'/'.$fileName;
$pdf->Output($fullPath, 'F');

return Yii::$app->response->sendFile($fullPath,$fileName);


