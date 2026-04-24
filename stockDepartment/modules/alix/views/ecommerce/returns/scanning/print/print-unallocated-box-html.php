<?php

use yii\helpers\Html;

$table = Html::beginTag('div', ['class' => 'a4']);
$table .= '<table width="100%" cellspacing="0" cellpadding="4" border="1">'
    . '<tr align="center" valign="middle">'
    . '<th width="100%"><strong>' . Yii::t('inbound/forms', 'Primary address') . '</strong></th>'
    . '</tr>';

if (!empty($items)) {
    foreach ($items as $item) {
        $table .= '<tr align="center" valign="middle">'
            . '<td>' . $item['primary_address'] . '</td>'
            . '</tr>';
    }
}

$table .= '</table>';
$table .= Html::endTag('div');
echo $table;
