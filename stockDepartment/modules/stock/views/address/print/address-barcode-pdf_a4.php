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
//define('K_TCPDF_CALLS_IN_HTML', true);
$pdf = new TCPDF( 'L', 'mm', 'A4', true, 'UTF-8');
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
$pdf->SetMargins(5, 5, 5, true);
//set auto page breaks
$pdf->SetAutoPageBreak(false, 0);
//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');


//$addresses = ['0-1-2-3-4','2-1-2-3-4'];
// 7-1-31-1/7-1-44-1
$addresses =  \common\modules\stock\models\RackAddress::find()
                ->select('address')
//                ->andWhere('`warehouse_id` = 0 AND `address_unit1` = 7 AND `address_unit2` = 1 AND `address_unit3` >= 31 AND `address_unit3` <= 44 AND `address_unit4` = 1')
                ->andWhere(['warehouse_id'=> 405])
                //->andWhere(['warehouse_id'=> 118])
                //->andWhere(['address_unit1'=> 151])
//                ->andWhere(['address_unit2'=> 1])
//                ->andWhere(['address_unit4'=> 1])
//                ->limit(1)
                ->asArray()
                ->column();

$pdf->AddPage('L', 'A4', true);
$stepX = 100;
$stepSumX = 5;

$stepY = 50;
$stepSumY = 5;

$row = 0;
//$i = 1;
foreach ($addresses as $address) {

//    $pdf->AddPage('L', 'NOMADEX70X100', true);
//    $pdf->AddPage('L', 'A4', true);
//    $pdf->Ln(3);
    $style = array(
//        'position' => 'L',
        'align' => 'C',
        'stretch' => false,
        'fitwidth' => false,
        'cellfitalign' => '',
        'border' => false,
        'padding' => 0,
        'hpadding' => 0,
        'vpadding' => 0.5,
        'fgcolor' => array(0, 0, 0),
        'bgcolor' => false, //array(255,255,255),
        'text' => false,
        'font' => 'arial',
        'fontsize' => 40,
        'stretchtext' => 1
    );

    $pdf->write1DBarcode($address, 'C128', $stepSumX, $stepSumY, 85, 10, 0.8, $style);
    $pdf->SetFont('Arial','B',43);
//
    $pdf->writeHTMLCell(0,0,$stepSumX-3,$stepSumY+10,"<h1>".$address."</h1>",0,0,false,false,'L');
    $stepSumX += $stepX;
    $row++;

    if ($row % 3 == 0) {
        $stepSumX = 5;
        $stepSumY += $stepY;
    }
    if ($row % 3 && $row != 0) {
        $stepSumX -= 5;
    }

    if ($row % 12 == 0) {
        $stepSumX = $stepSumY = 5;
        $row = 0;
        $pdf->AddPage('L', 'A4', true);
    }
}
//$params = $pdf->serializeTCPDFtagParameters(array('CODE 128', 'C128', '', '', 80, 30, 0.4, array('position'=>'S', 'border'=>true, 'padding'=>4, 'fgcolor'=>array(0,0,0), 'bgcolor'=>array(255,255,255), 'text'=>true, 'font'=>'helvetica', 'fontsize'=>8, 'stretchtext'=>4), 'N'));
//$htmlBARCODE = '<tcpdf method="write1DBarcode" params="'.$params.'" />';

//$html = '<table border="1" cellspacing="0" cellpadding="0"  style="width: 100%"><tr><td>'.$htmlBARCODE.'</td><td>'.$htmlBARCODE.'</td></tr></table>';

// output the HTML content
//$pdf->SetXY(0,0);
//$pdf->writeHTML($html, true, 0, true, 0);

$pdf->lastPage();
$pdf->Output($address . '-address-barcode.pdf', 'D');
Yii::$app->end();