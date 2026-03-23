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
?>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <b>Добрый день,</b><br />
    Для первозчика  <?= $agentTitle ?> не найден тариф для подмаршрута из <br />
    <?= $fromPointTitle ?> <br />
    в <br />
    <?= $toPointTitle ?><br />
    Номер заявки на доставку № <?= Html::a($deliveryProposalTitle,Url::to(Yii::$app->params['stockDepartmentUrl'].'/tms/default/view/?id='.$dpID,true)); ?>
</p>