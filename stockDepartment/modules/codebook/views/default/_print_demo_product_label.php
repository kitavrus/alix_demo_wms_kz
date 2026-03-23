<?php
/**
* Created by PhpStorm.
* User: Igor
* Date: 03.08.14
* Time: 12:24
*/
use stockDepartment\modules\order\models\OrderProcess;
use common\modules\store\models\Store;

//$store = Store::findOne($store_id);
//if(!empty($items)) {
$productPrice = '4250';

$productNameRu = 'кардиган';
$productNameKz = 'кардиган'; // нет казахской верии

$productModel = 'T77-1156L';
$productBarcode = '01341102';

$productCompositionRu = '50% Хлопок 19% полиэстер 19% полиамид 12% металл';
$productCompositionKz = '50% Мақта 19% полиэстер 19% полиамид 12% металл'; // НЕТ казахской верии

$productMadeInRu = 'КАМБОДЖАДА ЖАСАЛҒАН'; // НЕТ русской верии
$productMadeInKz = 'КАМБОДЖАДА ЖАСАЛҒАН';

$exporterRu = 'МАРКС ЭНД СПЕНСЕР ПЛС, Ватерсайд Хаус, 35 Норт Уорф Роуд, Лондон W2 1NW, Соединённое Королевство Великобритании и Северной Ирландии';
$exporterKz = 'МАРКС ЭНД СПЕНСЕР ПЛС, Ватерсайд Хаус, 35 Норт Уорф Роуд, Лондон қаласы W2 1NW,  Ұлыбритания және Солтүстік Ирландия Біріккен Корольдігі';

$importerRu = 'Фэшн Ритейл Казахстан ТОО: город Алматы, ул. Шевченко 157, Республика Казахстан. Тел.: +7(727)3210798';
$importerKz = 'Фэшн Ритейл Казахстан ЖШС Мекенжайы: Алматы қаласы, Шевченко көшесі, 157, Қазақстан Республикасы. Тел.: +7(727)3210798';


////Yii::$app->get('tcpdf');;;

$pdf = new TCPDF( 'P', 'mm', 'A4', true, 'UTF-8');

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('nomadex.com');
$pdf->SetTitle('Product labels');
$pdf->SetSubject('Product labels');
$pdf->SetKeywords('nomadex.com, product, label');

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

$i = 1;
$pdf->AddPage('L', 'NOMADEX40X60', true);


$pdf->setJPEGQuality(100);
$eacImgPath = Yii::getAlias("@web/image/pdf/");
$pdf->Image($eacImgPath . 'eac-logo.jpg', 0, 1, 0, 0, 'jpg', 'http://nomadex.com', 'N', false, 300, 'R', false, false, 0, false, false, false);

// Product price
$pdf->SetFont('arial', 'b', 9);
$pdf->MultiCell(0,0, Yii::$app->formatter->asCurrency($productPrice) , 0, 'L', false, 1, '1', '1', true, 0, false, true, 12.2, 'T', true);

// Product name
$productName = $productNameRu . ' / ' . $productNameKz;
$productName = $productNameRu . ' ( '.$productModel.' )';

$pdf->SetFont('arial', 'b', 6);
$pdf->MultiCell(0,0, $productName , 0, 'L', false, 1, '1', '5', true, 0, false, true, 12.2, 'T', true);

$fontSizeBig = 12;
$fontSizeMiddle = 6;
$fontSizeSmall = 5;

$htmlTable = '<table width="100%" cellspacing="0" cellpadding="1" border="0">';

// Product composition
$productComposition = $productCompositionRu . ' / ' . $productCompositionKz;

$htmlTable .='<tr valign="bottom" >' .
'        <td width="100%" align="left" style="font-weight:bold; font-size:' . $fontSizeMiddle . 'px;">' . $productComposition . ':</td>' .
'    </tr>';

// Product made in
$productMadeIn = $productMadeInRu . ' / ' . $productMadeInKz;
$productMadeIn = $productMadeInRu;

$htmlTable .='<tr valign="bottom" >' .
'        <td width="100%" align="left" style="font-weight:bold;font-size:' . $fontSizeSmall . 'px;">' . $productMadeIn. ':</td>' .
'    </tr>';

// Product exporter
$exporter = $exporterRu . ' / ' . $exporterKz;

$htmlTable .='<tr valign="bottom" >' .
'        <td width="100%" align="left" style="font-weight:bold;font-size:' . $fontSizeSmall . 'px;">' . $exporter . ':</td>' .
'    </tr>';

// Product importer
$importer = $importerRu . ' / ' . $importerKz;

$htmlTable .='<tr valign="bottom" >' .
'        <td width="100%" align="left" style="font-weight:bold;font-size:' . $fontSizeSmall . 'px;">' . $importer . ':</td>' .
'    </tr>';


$htmlTable .= '</table>';

$pdf->writeHTMLCell(0,0,0,8,$htmlTable);

$style = array(
'position' => 'C',
'align' => 'C',
'stretch' => true,
'fitwidth' => true,
'cellfitalign' => '',
'border' => false,
'padding' => 0,
'hpadding' => 0,
'vpadding' => 0.5,
'fgcolor' => array(0, 0, 0),
'bgcolor' => false, //array(255,255,255),
'text' => true,
//'font' => 'helvetica',
'font' => 'arial',
'fontsize' => 7,
'stretchtext' => 4
);

$pdf->write1DBarcode($productBarcode, 'C128', '0', '31', '',9, 0.5, $style, 'N');

$pdf->Output($productModel . '-box-label.pdf', 'D');
Yii::$app->end();