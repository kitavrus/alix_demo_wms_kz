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

?>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <b>Добрый день,</b><br />
    Сегодня будет поставка, не забудьте взять заявку на прием товара.<br />
    Более подробную информацию о поставке вы можете посмотреть по <?= Html::a('ссылке',\yii\helpers\Url::to('http://client-wms.nmdx.kz/transportLogistics/tl-delivery-proposal/view/?id='.$dpModel->id,true)); ?>
</p>
