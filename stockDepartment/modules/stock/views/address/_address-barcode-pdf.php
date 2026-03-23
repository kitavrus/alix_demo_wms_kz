<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 24.02.15
 * Time: 10:15
 */


/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $codeBookModel common\modules\codebook\models\Codebook */

////Yii::$app->get('tcpdf');;;

//$ttn = sprintf("%014d",$model->id);
//E: печатаем разные этикетки для разных клиентов


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
$pdf->SetMargins(5, 5, 5, false);

//set auto page breaks
$pdf->SetAutoPageBreak(false, 0);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
$style = array(
    'border'=>true,
    'padding'=>5,
    'hpadding'=>5,
    'vpadding'=>5,
    'fgcolor'=>array(0, 0, 0),
    'bgcolor'=>false,
    'text'=>true,//Текст снизу
    'font'=>'dejavusans',
    'fontsize'=>10,//Размер шрифта
    'stretchtext'=>4,//Растягивание
    'stretch'=>true,
    'fitwidth'=>true,
    'cellfitalign'=>'',
    'position'=>'L',
    'align'=>'C',
);
$pdf->AddPage('R', 'NOMADEX70X100', true);
$pdf->Ln(15);
$pdf->write1DBarcode($address, 'C128', 5,5, 100, 30, 1.5, $style, 'L');
$pdf->lastPage();

$pdf->Output($address . '-address-barcode.pdf', 'D');

Yii::$app->end();