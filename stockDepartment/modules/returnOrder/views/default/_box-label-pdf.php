<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 24.02.15
 * Time: 10:15
 */

use common\modules\client\models\ClientEmployees;
use common\modules\transportLogistics\components\TLHelper;

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
//$pdf->AddPage('L', 'NOMADEX70X100', true);
$pdf->AddPage('P', 'NOMADEX70X100', true);

$style = array(
	'border'=>false,
	'padding'=>0,
	'hpadding'=>0,
	'vpadding'=>0.5,
	'fgcolor'=>array(0, 0, 0),
	'bgcolor'=>false,
//		'text'=>true,//Текст снизу
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

$orderBarcode = $model->order_number;

$lotBarcode = isset($koliResponseData['KoliBarkod']) ? $koliResponseData['KoliBarkod'] : '-';
$merchGroup = isset($koliResponseData['IrsaliyeMerchGroup']) ? $koliResponseData['IrsaliyeMerchGroup'] : '-';
$buyerGroup = isset($koliResponseData['IrsaliyeBuyerGroup']) ? $koliResponseData['IrsaliyeBuyerGroup'] : '-';
$irsaliyeNo = isset($koliResponseData['IrsaliyeNo']) ? $koliResponseData['IrsaliyeNo'] : '-';
$gonMagaza = isset($koliResponseData['DepoTanim']) ? $koliResponseData['DepoTanim'] : '-';

$seasonValue = isset($koliResponseData['Sezon']) ? $koliResponseData['Sezon'] : '-';
$urunKodu = isset($koliResponseData['UrunKodu']) ? $koliResponseData['UrunKodu'] : '-';
$lotCount = isset($koliResponseData['IrsaliyeMiktar']) ? (int)$koliResponseData['IrsaliyeMiktar'] : '-';


$pdf->write1DBarcode($orderBarcode, 'C128', 0, 0, '60', 15, 1.5, $style, 'C');

$pdf->SetFont('arial', 'b', 8);


$htmlBox = '<span style="font-weight:bold; font-size: 4mm; text-align: right;">'.$urunKodu.'</span>';

$pdf->writeHTMLCell('67', '8', 1, 17, $htmlBox,0,0,false,true,'R');

$htmlBox =
'<table border="0" cellspacing="0" cellpadding="0"  style=" width: 100%">
	<tr>
		<td>';
// LEFT
$htmlBox .=
			'<table border="0" cellspacing="0" cellpadding="0"  style="-line-height: 2; width: 100%">'.
				'<tr>'
					.'<td style="font-weight:normal; font-size: 3mm; text-align: left; width: 90%;  border-bottom: 1px solid black;">
						SEASON'
					.'</td>'
					.'<td style=" width: 10%; border-bottom: none; border-right: 1px solid black;"></td>'
				.'</tr>'
			.'<tr>'
				.'<td style="border-right: 1px solid black; font-weight:normal; font-size: 3mm; text-align: left; width: 100%">'
						.$seasonValue
				.'</td>'
			.'</tr>';

$htmlBox .=
			'<tr >'
				.'<td colspan="2" style="width: 100%; height:30px; border-bottom: none; border-right: 1px solid black;"></td>'
			.'</tr>';

$htmlBox .=
	'<tr>'
		.'<td style="font-weight:normal; font-size: 3mm; text-align: left; width: 90%; border-bottom: 1px solid black;">
			MERCH GROUP'
		.'</td>'
	.'<td style=" width: 10%; border-bottom: none; border-right: 1px solid black;"></td>'
	.'</tr>'
	.'<tr>'
		.'<td style="border-right: 1px solid black; font-weight:normal; font-size: 3mm; text-align: left; width: 100%">'
			.$merchGroup
		.'</td>'
	.'</tr>';

$htmlBox .=
	'<tr >'
	.'<td colspan="2" style="width: 100%; height:30px; border-bottom: none; border-right: 1px solid black;"></td>'
	.'</tr>';

$htmlBox .=
	'<tr>'
	.'<td style="font-weight:normal; font-size: 3mm; text-align: left; width: 90%; border-bottom: 1px solid black;">
			BUYER GROUP'
	.'</td>'
	.'<td style=" width: 10%; border-bottom: none; border-right: 1px solid black;"></td>'
	.'</tr>'
	.'<tr>'
	.'<td style="border-right: 1px solid black; font-weight:normal; font-size: 3mm; text-align: left; width: 100%">'
			.$buyerGroup
	.'</td>'
	.'</tr>';

$htmlBox .= '</table>';
$htmlBox .= '</td>';
$htmlBox .= '<td>';
//RIGHT
$htmlBox .=
	'<table border="0" cellspacing="0" cellpadding="0"  style="width: 100%">'.
	'<tr>'
		.'<td style="width: 10%; border-bottom: none; border-right: none;"></td>'
		.'<td style="font-weight:normal; font-size: 3mm; text-align: left; width: 90%;  border-bottom: 1px solid black;">
			Lot icindeki urun adeti'
		.'</td>'
	.'</tr>'
	.'<tr>'
	.'<td style="width: 10%; border-bottom: none; border-right: none;"></td>'
	.'<td style="border-right: none; font-weight:normal; font-size: 3mm; text-align: left; width: 100%">'
		.$lotCount
	.'</td>'
	.'</tr>';

$htmlBox .=
	'<tr >'
	.'<td colspan="2" style="width: 100%; height:30px; border-bottom: none; border-right: none;"></td>'
	.'</tr>';

$htmlBox .=
	'<tr>'
	.'<td style="width: 10%; border-bottom: none; border-right: none;"></td>'
	.'<td style="font-weight:normal; font-size: 3mm; text-align: left; width: 90%;  border-bottom: 1px solid black;">
			IRSALIYE NO'
	.'</td>'
	.'</tr>'
	.'<tr>'
	.'<td style="width: 10%; border-bottom: none; border-right: none;"></td>'
	.'<td style="border-right: none; font-weight:normal; font-size: 3mm; text-align: left; width: 100%">'
		.$irsaliyeNo
	.'</td>'
	.'</tr>';

$htmlBox .=
	'<tr >'
	.'<td colspan="2" style="width: 100%; height:30px; border-bottom: none; border-right: none;"></td>'
	.'</tr>';

$htmlBox .=
	'<tr>'
	.'<td style="width: 10%; border-bottom: none; border-right: none;"></td>'
	.'<td style="font-weight:normal; font-size: 3mm; text-align: left; width: 90%;  border-bottom: 1px solid black;">
			GON MAGAZA'
	.'</td>'
	.'</tr>'
	.'<tr>'
	.'<td style="width: 10%; border-bottom: none; border-right: none;"></td>'
	.'<td style="border-right: none; font-weight:normal; font-size: 3mm; text-align: left; width: 100%">'
		.$gonMagaza
	.'</td>'
	.'</tr>';

$htmlBox .= '</table>';

$htmlBox .= '</td></tr>';

$htmlBox .= '</table>';
$pdf->writeHTMLCell('67', '8', 1, 25, $htmlBox,false);

$style = array(
	'border'=>false,
	'padding'=>0,
	'hpadding'=>0,
	'vpadding'=>0.5,
	'fgcolor'=>array(0, 0, 0),
	'bgcolor'=>false,
//		'text'=>true,//Текст снизу
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


$pdf->write1DBarcode($lotBarcode, 'C128', 0, 85, '60', 15, 1.5, $style, 'C');

$pdf->lastPage();

$pdf->Output(time() . 'return-box-label.pdf', 'D');
die;