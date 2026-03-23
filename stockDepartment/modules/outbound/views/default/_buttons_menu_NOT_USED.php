<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 02.02.15
 * Time: 17:14
 */
//use yii;
use yii\helpers\Html;
?>

<?= Html::tag('span', Yii::t('outbound/buttons', 'Load outbound order API'), ['class' => 'btn btn-primary btn-lg', 'style' => ' margin-left:10px;', 'id' => 'confirm-outbound-order-api-bt']) ?>
<?= Html::tag('span', Yii::t('outbound/buttons', 'Confirm outbound order API'), ['class' => 'btn btn-primary btn-lg', 'style' => ' margin-left:10px;', 'id' => 'confirm-outbound-order-api-bt']) ?>
<?= Html::tag('span', Yii::t('outbound/buttons', 'Outbound print pick list'), ['class' => 'btn btn-primary btn-lg', 'style' => ' margin-left:10px;', 'id' => 'outbound-print-pick-list-bt']) ?>
<?= Html::tag('span', Yii::t('outbound/buttons', 'Begin end picking process'), ['class' => 'btn btn-primary btn-lg', 'style' => ' margin-left:10px;', 'id' => 'begin-picking-process-bt']) ?>
