<?php
use yii\helpers\Html;
$structure_table = Html::beginTag('div', ['class'=>'a4']);
$structure_table .= '<table width="100%" cellspacing="0" cellpadding="4" border="1">' .
    '   <tr align="center" valign="middle" >' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '      <th width="15%" align="center" valign="middle" border="1"><strong>' . Yii::t('inbound/forms','Primary address') . '</strong></th>' .
    '   </tr>';

if (!empty($items)) {
    $data = array_chunk($items, 6);
   // \yii\helpers\VarDumper::dump($data, 10, true); die;
    foreach ($data as $items) {
            $structure_table .= '<tr align="center" valign="middle">';
            foreach($items as $item){
                $structure_table .= '<td align="center" valign="middle" border="1">' . $item['primary_address'] . '</td>';
            }
            $structure_table .= '</tr>';

    }
}

$structure_table .= '</table>';
$structure_table .= Html::endTag('div');
echo $structure_table;