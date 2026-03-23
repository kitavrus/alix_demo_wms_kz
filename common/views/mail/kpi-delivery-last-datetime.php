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
    Были найдены заявки на доставку, у которых последний день доставки:<br />
    <table border="1" width="100%">
        <tr>
            <th>из</th>
            <th>в</th>
            <th>срок доставки ( в днях )</th>
            <th>разница дни</th>
            <th>разница часы</th>
            <th>отгрузили</th>
            <th>сейчас</th>
            <th>подробней</th>
        </tr>

    <?php foreach($data as $d) { ?>
        <tr>
            <td><?php echo "".$d['store_from']."" ; ?></td>
            <td><?php echo $d['store_to'] ; ?></td>
            <td><?php echo $d['delivery_term'] ; ?></td>
            <td><?php echo $d['diff_days'] ; ?></td>
            <td><?php echo $d['diff_hours'] ; ?></td>
            <td><?php echo $d['start'] ; ?></td>
            <td><?php echo $d['now'] ; ?></td>
            <td><?php echo  Html::a('Перейти',\yii\helpers\Url::to(Yii::$app->params['stockDepartmentUrl'].'/tms/default/view/?id='.$d['id'],true)); ?></td>
        </tr>
    <?php } ?>

    </table>
</p>