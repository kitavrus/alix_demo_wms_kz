<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 18.11.14
 * Time: 10:49
 */
use yii\helpers\Html;
use yii\helpers\Url;
/**
 * @var common\modules\transportLogistics\models\TlDeliveryProposal $dpModel
 *
 */
$clientTitle = '';
$clientID = '';
if($rClient =  $dp->client) {
    $clientTitle = $rClient->title;
    $clientID = $rClient->id;
}
?>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <b>Добрый день,</b><br />
    Для клиента <?= $clientTitle ?> c id <?= $clientID ?>  отсутствует индивидуальный тариф для маршрута по заявке № <?=$dp->id?>, хотя
    присутствуют индивидуальные тарифы для других маршрутов.
</p>