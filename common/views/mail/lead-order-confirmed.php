<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.10.14
 * Time: 11:25
 */
use yii\helpers\Html;

/**
 * @var common\modules\transportLogistics\models\TlDeliveryProposal $dpModel
 * @var common\components\MailManager $url
 */
$clientTitle = '';
if($rClient =  $order->client) {
    $clientTitle = $rClient->full_name;
}
?>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <b><?=Yii::t('client/mail', 'Hello, {username}', ['username'=>$clientTitle])?></b><br />
    <?=Yii::t('client/mail', 'Your order №{orderNumber} was confirmed', ['orderNumber'=>$order->order_number])?>.<br />
    Отследить статус заявки вы можете в <?= Html::a('личном кабинете NMDX',\yii\helpers\Url::to(Yii::$app->params['personalBranchUrl'].'/order/default/view?id='.$order->id, true)); ?>
</p>
