<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 03.11.14
 * Time: 09:34
 */
use common\modules\client\models\ClientEmployees;
use common\modules\transportLogistics\components\TLHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $codeBookModel common\modules\transportLogistics\models\TlDeliveryProposal */

////Yii::$app->get('tcpdf');;

//$managersNames = [];
$managersNamesTo = '';
$storeTitleTo = '';

if($routeTo = $model->routeTo) {

    // находим всех директоров магазина и отправляем им имейлы
    $clientEmployees = ClientEmployees::find()
        ->where([
            'deleted'=>0,
            'client_id'=>$model->client_id,
            'store_id'=>$routeTo->id,
            'manager_type'=>[
                ClientEmployees::TYPE_BASE_ACCOUNT,
                ClientEmployees::TYPE_DIRECTOR,
                ClientEmployees::TYPE_DIRECTOR_INTERN,
            ]
        ])
        ->all();

    foreach($clientEmployees as $item) {
        $managersNamesTo .= $item->first_name.' '.$item->last_name.' / '.$item->phone_mobile.' '.$item->phone."\n";
    }
}

$value = TLHelper::getStoreArrayByClientID($routeTo->client_id);
$storeTitleTo = isset ($value[$routeTo->id]) ? $value[$routeTo->id]: $routeTo->name;


// From Store
$managersNamesFrom = '';
$storeTitleFrom = '';

if($routeFrom = $model->routeTo) {

    // находим всех директоров магазина и отправляем им имейлы
//    $clientEmployees = ClientEmployees::find()
//        ->where([
//            'deleted'=>0,
//            'client_id'=>$model->client_id,
//            'store_id'=>$routeTo->id,
//            'manager_type'=>[
//                ClientEmployees::TYPE_BASE_ACCOUNT,
//                ClientEmployees::TYPE_DIRECTOR,
//                ClientEmployees::TYPE_DIRECTOR_INTERN,
//            ]
//        ])
//        ->all();
//
//    foreach($clientEmployees as $item) {
//        $managersNamesTo .= $item->first_name.' '.$item->last_name.' / '.$item->phone_mobile.' '.$item->phone."\n";
//    }
}

$value = TLHelper::getStoreArrayByClientID($routeFrom->client_id);
$storeTitleTo = isset ($value[$routeFrom->id]) ? $value[$routeFrom->id]: $routeFrom->name;





$boxQty = $model->number_places_actual;

$from = $codeBookModel->barcode;
$to = $from + $boxQty;
$pref = $codeBookModel->cod_prefix;
$codeBookModel->barcode = $to;
$codeBookModel->save(false);
$from += 1;

$boxes = [];

for ($i = $from; $i <= $to; $i++) {
    $boxes[] = $pref . sprintf("%010d", $i);
}

$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetFont('dejavusans', '', 8);
$pdf->SetMargins(10, 5, 10);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setFooterMargin(0);

$config_array = array(
    1 => array(
        'coordinates' => array(
            'x' => 10,
            'y' => 5,
        ),
        'position' => 'L',
        'multicells' => array(
            1 => array(
                'x' => 10,
                'y' => 50,
            ),
            2 => array(
                'x' => 10,
                'y' => 65,
            ),
            3 => array(
                'x' => 10,
                'y' => 85,
            ),
        )
    ),
    2 => array(
        'coordinates' => array(
            'x' => 150,
            'y' => 5,
        ),
        'position' => 'R',
        'multicells' => array(
            1 => array(
                'x' => 165,
                'y' => 50,
            ),
            2 => array(
                'x' => 165,
                'y' => 65,
            ),
            3 => array(
                'x' => 165,
                'y' => 85,
            ),
        )
    ),
    3 => array(
        'coordinates' => array(
            'x' => 0,
            'y' => 110,
        ),
        'position' => 'L',
        'multicells' => array(
            1 => array(
                'x' => 10,
                'y' => 155,
            ),
            2 => array(
                'x' => 10,
                'y' => 170,
            ),
            3 => array(
                'x' => 10,
                'y' => 185,
            ),
        )
    ),
    4 => array(
        'coordinates' => array(
            'x' => 150,
            'y' => 110,
        ),
        'position' => 'R',
        'multicells' => array(
            1 => array(
                'x' => 165,
                'y' => 155,
            ),
            2 => array(
                'x' => 165,
                'y' => 170,
            ),
            3 => array(
                'x' => 165,
                'y' => 185,
            ),
        )
    ),
);

$style = array(
    'border' => false,
    'padding' => 0,
    'hpadding' => 0,
    'vpadding' => 0.5,
    'fgcolor' => array(0, 0, 0),
    'bgcolor' => false,
    'text' => true,//Текст снизу
    'font' => 'dejavusans',
    'fontsize' => 15,//Размер шрифта
    'stretchtext' => 4,//Растягивание
    'stretch' => true,
    'fitwidth' => true,
    'cellfitalign' => '',
);

//$codesCount = count($codes);
$boxCount = count($boxes);


//\yii\helpers\VarDumper::dump($boxes,10,true);
//die('YPA');
$i = 1;
$num = 1;
//foreach ($codes as $code) {
foreach ($boxes as $barcode) {

    if ($i == 1) { //Если первый то добавляем страницу и рисуем линии
        $pdf->AddPage();
        $pdf->Line(145, 0, 145, 210);//вертикальная
        $pdf->Line(0, 105, 297, 105);//горизонтальная
    }

    $pdf->SetXY($config_array[$i]['coordinates']['x'], $config_array[$i]['coordinates']['y']);

    $style['position'] = $config_array[$i]['position'];
    $style['align'] = $config_array[$i]['position'];

    $pdf->write1DBarcode($barcode, 'C128', '', '', '120', 45, 1.8, $style, 'C');
    $pdf->SetFont('dejavusans', '', 12);

    $pdf->MultiCell(120, 0,
//        "Имя.  Телефон",
        $storeTitle,
        0, 'C', false, 1,
        $config_array[$i]['multicells'][1]['x'],
        $config_array[$i]['multicells'][1]['y'],
        true, 0, false, true, 12.2, 'T', true);

    $pdf->MultiCell(120, 0,
//        "Тут адрес",
        $managersNames,
        0, 'C', false, 1,
        $config_array[$i]['multicells'][2]['x'],
        $config_array[$i]['multicells'][2]['y'],
        true, 0, false, true, 12.2, 'T', true);

    $pdf->MultiCell(120, 4,
//        $num . " из " . $codesCount,
        $num . " из " . $boxCount,
        0, 'C', false, 1,
        $config_array[$i]['multicells'][3]['x'],
        $config_array[$i]['multicells'][3]['y'],
        true, 0, false, true, 12.2, 'T', true);

    if ($i == 4) {
        $i = 1;
    } else {
        $i++;
    }

    $num++;
}


//$pdf->Output('example_006.pdf', 'I');
$pdf->lastPage();

$pdf->Output(time() . '-box-label.pdf', 'D');
Yii::$app->end();