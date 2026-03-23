<?php
use common\components\BarcodeManager;
use yii\helpers\Html;

$registryBarcode = sprintf("%014d", $model->id);

$structure_table = Html::beginTag('div', ['class' => 'a4 picking-list']);
$barcodeIMG = BarcodeManager::createBarcodeImage($registryBarcode, 0, true, 60, 580, 290, 3);
$structure_table .= Html::img($barcodeIMG, ['class'=>'h-picking-list-barcode']);
$structure_table .= Html::tag('p', '<b>Водитель:</b> '.$model->driver_name);
$structure_table .= Html::tag('p', '<b>Авто: </b>'.$model->car->title);
$structure_table .= Html::tag('p', '<b>Номер: </b>'.$model->driver_auto_number);
$structure_table .= Html::tag('p', '<b>Дата: </b>'.date('Y.m.d H:i:s'));
$structure_table .= Html::tag('h2', '<b>Лист отгрузки №</b>'.$registryBarcode, ['style' => 'text-align: center']);

$structure_table .= '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
    '   <tr align="center" valign="middle" >' .
    '      <th width="6%" align="center" valign="middle" border="1"><strong>' . Yii::t('client/forms','ТТН') . '</strong></th>' .
    '      <th width="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('client/forms','Point from') . '</strong></th>' .
    '      <th width="25%" align="center" valign="middle" border="1"><strong>' . Yii::t('client/forms','Point to') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('client/forms','Weight') . '</strong></th>' .
    '      <th width="10%" align="center" valign="middle" border="1"><strong>' . Yii::t('client/forms','Volume') . '</strong></th>' .
    '      <th width="5%" align="center" valign="middle" border="1"><strong>' . Yii::t('client/forms','Места') . '</strong></th>' .
    '      <th width="19%" align="center" valign="middle" border="1"><strong>' . Yii::t('transportLogistics/forms','Orders') . '</strong></th>' .
    '   </tr>';


if ($items = $model->registryItems) {
    foreach ($items as $item) {
        $structure_table .= '<tr align="center" valign="middle">
                <td align="left" valign="middle" border="1">'.$item->tl_delivery_proposal_id.'</td>
                <td align="center" valign="middle" border="1" >'.$item->routeFrom->getDisplayFullTitle().'</td>
                <td align="center" valign="middle" border="1" >'.$item->routeTo->getDisplayFullTitle().'</td>
                <td align="center" valign="middle" border="1" >'.$item->weight .'</td>
                <td align="center" valign="middle" border="1" >'.$item->volume.'</td>
                <td align="center" valign="middle" border="1" >'.$item->places.'</td>
                <td align="center" valign="middle" border="1" >'.str_replace(', ', '<br>', $item->getExtraFieldValueByName('orders')).'</td>
            </tr>';



    }
}

$structure_table .= '</table>';

$structure_table .= Html::tag('p', '<b>Общий вес (кг):</b> '.$model->weight);
$structure_table .= Html::tag('p', '<b>Общий объём (м³):</b> '.$model->volume);
$structure_table .= Html::tag('p', '<b>Всего мест:</b> '.$model->places);
$structure_table .= Html::tag('span', '<b>Сдал:_________________</b>');
$structure_table .= Html::tag('span', '<b>Принял:________________</b>');

echo $structure_table;