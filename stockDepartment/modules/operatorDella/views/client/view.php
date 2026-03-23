<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model common\modules\client\models\Client */

$this->title = Yii::t('client/titles', 'Client №').$model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('client/titles', 'Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="client-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('forms/_client_points_form',['model'=>$model]) ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'full_name',
            'username',
            'phone',
            'phone_mobile',
            'email:email',
           [   'label' => Yii::t('client/forms', 'Client Type'),
               'attribute'=>'client_type',
               'value' => $model->getClientTypeValue(),
           ],
            [
                'attribute'=>'status',
                'value' => $model->getStatus(),
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>
</div>

<script type="text/javascript">
    $(function(){
        var body = $('body');

        body.on('click', '#quick-order', function(d){
            var res = $('#delivery-result'),
                cost = res.data('cost');
            window.location.href = '/operatorDella/order/quick-order?sender='
            +$('#sender').val()
            +'&recipient=' +$('#recipient').val()
            +'&weight='+$('#weight').val()
            +'&volume='+$('#volume').val()
            +'&delivery_type='+$('#delivery-type').val()
            +'&cost='+cost
            +'&client_id='+$(this).data('client');
        });
        body.on('click', '#pre-calculate', function(d){
            var res = $('#delivery-result');
            $.post(
                '/operatorDella/tariff/pre-calculate-price',
                {
                    client_id:$(this).data('client'),
                    delivery_type:$('#delivery-type').val(),
                    from_route_id:$('#sender').val(),
                    to_route_id:$('#recipient').val(),
                    weight:$('#weight').val(),
                    volume:$('#volume').val()
                }
            ).done(function (result) {
                    console.log(result.data);
                    res.find('h2').html(result.data);
                    res.data('cost', result.data);
                    res.removeClass('hidden');
                }).fail(function () {
                    console.log("server error");
                });

            return false;
        });
    });

</script>