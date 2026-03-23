<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 18.11.14
 * Time: 18:36
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
    Клиент создал новый отзыв на заявку на доставку<br />
    Более подробную информацию об отзыве вы можете посмотреть по <?= Html::a('ссылке',Url::to('http://wms.nmdx.kz/store/store-review/view?id='.$model->id,true)); ?>
</p>