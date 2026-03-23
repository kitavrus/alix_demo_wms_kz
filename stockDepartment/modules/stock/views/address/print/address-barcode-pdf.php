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
$pdf->SetMargins(5, 5, 5, false);
//set auto page breaks
$pdf->SetAutoPageBreak(false, 0);
//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');


$style = array(
    'position' => 'C',
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

$addresses =  \common\modules\stock\models\RackAddress::find()
                ->select('address')
                ->andWhere(['warehouse_id'=> 3])
				->andWhere(['address_unit1'=> 7])
				->andWhere(['address_unit2'=> 1])
				//->andWhere('address_unit3 <= 30')

	// warehouse_id` = '3' AND `address_unit1` = '7' AND `address_unit2` = '1' AND `address_unit3` <= '30'
//                ->andWhere(['address_unit1'=> 8])
//                ->andWhere(['warehouse_id'=> 42])
//                ->andWhere(['warehouse_id'=> 3])
//                ->andWhere("`address` LIKE '9-%'")
                ->asArray()
                ->column();


foreach ($addresses as $address) {
    $pdf->AddPage('L', 'NOMADEX70X100', true);
    $pdf->Ln(5);
    $pdf->write1DBarcode($address, 'C128', '', '', 100, 10, 0.8, $style, 'N');
    $pdf->SetFont('Arial','B',60);
    $pdf->writeHTML("<h1>".$address."</h1>",true,false,false,false,'C');
}
$pdf->lastPage();
$pdf->Output($address . '-address-barcode.pdf', 'D');
Yii::$app->end();