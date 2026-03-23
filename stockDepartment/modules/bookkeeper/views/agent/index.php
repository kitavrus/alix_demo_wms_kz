<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\VarDumper;
use stockDepartment\modules\bookkeeper\models\AgentsBookkeeperForm;
use common\models\ActiveRecord;
use common\modules\transportLogistics\models\TlAgents;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TlAgentsBookkeeperSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $agentArray  */
/* @var $monthArray  */
/* @var $activeAgentIDs  */
/* @var $agentsBookkeeperForm stockDepartment\modules\bookkeeper\models\AgentsBookkeeperForm  */

$this->title = Yii::t('app', 'Tl Agents Bookkeepers');
$this->params['breadcrumbs'][] = $this->title;
//\yii\helpers\VarDumper::dump($agentArray,10,true);
//VarDumper::dump($monthArray,10,true);
//die;
$fieldOptions = [
    'options' => ["class"=>'','tag'=>'span'],
    'template' => "{input}",
    'parts' => [
        '{beginWrapper}'=>'<span>',
        '{endWrapper}'=>'</span>',
    ]
];
?>
<?php foreach(TlAgents::getPaymentPeriodArray() as $payID=>$payValue) { ?>
    <?= Html::a($payValue, ['index', 'filter' => $payID], ['class' => 'btn '.($filter == $payID ? 'btn-warning' : 'btn-primary' )]) ?>
<?php } ?>
<br />
<br />
<table class="table table-bordered table-striped ">
    <tr>
        <td width="22%">Агенты</td>
    <?php foreach($monthArray as $month) { ?>
        <td><?php echo $month['start'].' - '.$month['end'].'  '.$month['monthTitle'] ?></td>
    <?php } ?>
    </tr>
    <?php $sumRows = []; ?>
    <?php foreach($agentArray as $agentID=>$agentValue) { ?>
        <?php if(array_key_exists($agentID,$activeAgentIDs)) { ?>
            <tr>
                <td><?php echo $agentValue ?></td>
                <?php foreach($monthArray as $month) { ?>

                    <?php $sumRowsKey = $month['start'].'-'.$month['end']; ?>
                    <?php if(!isset($sumRows[$sumRowsKey])) { ?>
                        <?php $sumRows[$sumRowsKey] = 0; ?>
                    <?php } ?>

                    <?php if(array_key_exists($agentID,$month['activeAgentIDs'])) { ?>

                        <?php $agentsBookkeeperForm->agent_id = $agentID; ?>
                        <?php $agentsBookkeeperForm->month_from = $month['start']; ?>
                        <?php $agentsBookkeeperForm->month_to = $month['end']; ?>
                        <?php $agentsBookkeeperForm->invoice = $month['activeAgentIDs'][$agentID]['sum_price_invoice']; ?>
                        <?php $agentsBookkeeperForm->status = $agentsBookkeeperForm->findStatus(); ?>

                        <?php if(in_array($agentsBookkeeperForm->findStatus(),[ActiveRecord::INVOICE_SET,ActiveRecord::INVOICE_NOT_SET])) { ?>
                            <?php $sumRows[$sumRowsKey] += $month['activeAgentIDs'][$agentID]['sum_price_invoice']; ?>
                        <?php } ?>

                        <td style="background-color:<?php echo AgentsBookkeeperForm::selectColorByStatus($agentsBookkeeperForm->status) ?>;" id="agent-bookkeeper-td-<?php echo $agentID.'_'.$month['start'].'_'.$month['end'] ?>">
                            <?php $form = ActiveForm::begin(['layout' => 'horizontal','id'=>'agent-bookkeeper-form-'.$agentID.'_'.$month['start'].'_'.$month['end']]); ?>

                            <?php echo $form->field($agentsBookkeeperForm, 'invoice',$fieldOptions)->hiddenInput()->error(false)->label(false);  ?>

                            <?php echo $form->field($agentsBookkeeperForm, 'agent_id',$fieldOptions)->hiddenInput()->error(false)->label(false);  ?>

                            <?php echo $form->field($agentsBookkeeperForm, 'month_from',$fieldOptions)->hiddenInput()->error(false)->label(false);  ?>

                            <?php echo $form->field($agentsBookkeeperForm, 'month_to',$fieldOptions)->hiddenInput()->error(false)->label(false);  ?>

                            К оплате : <strong><?php echo Yii::$app->formatter->asCurrency($month['activeAgentIDs'][$agentID]['sum_price_invoice']) ?></strong> <br />
                            Отчет : <?php echo Html::a('СКАЧАТЬ',['/tms/tl-delivery-proposal-route-cars/export-to-excel','TlDeliveryProposalRouteCarsSearch[shipped_datetime]'=>$month['start'].' / '.$month['end'],'TlDeliveryProposalRouteCarsSearch[agent_id]'=>$agentID])?> <br />
                            Список : <?php echo Html::a('перейти',['/tms/tl-delivery-proposal-route-cars/index','TlDeliveryProposalRouteCarsSearch[shipped_datetime]'=>$month['start'].' / '.$month['end'],'TlDeliveryProposalRouteCarsSearch[agent_id]'=>$agentID])?> <br />
                            Статус : <?php echo $form->field($agentsBookkeeperForm, 'status',$fieldOptions)->dropDownList(ActiveRecord::getInvoiceStatusArray(),['class'=>'field-agent-bookkeeper-form-status','data'=>['agent-id'=>$agentID,'month_from'=>$month['start'],'month_to'=>$month['end']]])->error(false)->label(false) ?> <br />

                            <?php ActiveForm::end(); ?>
                        </td>
                    <?php } else { ?>
                        <td>&nbsp;</td>
                    <?php } ?>
                <?php } ?>
            </tr>
        <?php } ?>
    <?php } ?>
    <tr>
        <td>&nbsp;</td>
        <?php foreach($sumRows as $sumRow) { ?>
            <?php echo "<td>Итого к оплате: <strong>".Yii::$app->formatter->asCurrency($sumRow)."</strong></td>"; ?>
        <?php } ?>
    </tr>
</table>

<?php
//VarDumper::dump($sumRows,10,true);
?>

<script type="application/javascript">
    $(function(){
        $('.field-agent-bookkeeper-form-status').on('change',function() {
            var agent_id = $(this).data('agent-id'),
                month_from = $(this).data('month_from');
                month_to = $(this).data('month_to');
            console.info(this);
            console.info($(this).data('agent-id'));
            $.post('/bookkeeper/agent/create', $('#agent-bookkeeper-form-'+agent_id+'_'+month_from+'_'+month_to).serialize(),function(data) {
                console.info(agent_id);
                console.info(data.color);
                console.info("#agent-bookkeeper-td-"+agent_id+'_'+month_from+'_'+month_to);
                $("#agent-bookkeeper-td-"+agent_id+'_'+month_from+'_'+month_to).css({'background-color':data.color});
            });
        });
    });
</script>