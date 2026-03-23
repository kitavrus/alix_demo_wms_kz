<?php
use common\modules\transportLogistics\components\TLHelper;

$this->title = Yii::t('frontend/titles', 'Calculate delivery');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row col-md-12">
<div class="calculator_form col-md-8">

    <?= $this->render('form/_calculator_form', ['model'=>$model]);?>
</div>
    <div class="col-md-4">
        <div id="delivery-result" class="panel panel-danger hidden">
            <div class="panel-heading">
                <strong> <?= Yii::t('frontend/titles', 'Delivery cost: ') ?></strong>
            </div>
            <div class="panel-body">
                <h2></h2>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        var b = $('body');
        b.on('submit', '#calculator_form', function(event) {
            event.preventDefault();
            var form = $('#calculator_form').serialize(),
                res = $('#delivery-result');
            $.post(
                'calculator',
                form
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

        b.on('click', '#clear', function() {
            var output = $('#delivery-result');
            $('#calculator_form').trigger('reset');
            output.addClass('hidden');
            output.find('h2').html('');
        });

        b.on('click', '#make-order', function(){
            var res = $('#delivery-result'),
                cost = res.data('cost');
            window.location.href = '/operatorDella/order/quick-order?'
            +'weight='+$('#deliverycalculatorform-weight').val()
            +'&volume='+$('#deliverycalculatorform-volume').val()
            +'&from_city_id='+$('#deliverycalculatorform-city_from').val()
            +'&to_city_id='+$('#deliverycalculatorform-city_to').val()
            +'&delivery_type='+$('#deliverycalculatorform-delivery_type').val()
            +'&cost='+cost;
        });

        b.on('click', '.link-bt', function(){
            window.location.href = $(this).data('url');
        });
    });

</script>