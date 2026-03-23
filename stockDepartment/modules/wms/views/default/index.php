<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = Yii::t('wms/titles', 'Distribution');
?>
<h1><?= $this->title ?></h1>

<?= Html::label(Yii::t('inbound/forms', 'Client ID')); ?>
<?= Html::dropDownList( 'client_id','',$clientsArray, [
        'data'=>['url'=>Url::to('/wms/default/route-form')],
        'prompt' => Yii::t('titles', 'Select client'),
        'id' => 'main-form-client-id',
        'class' => 'form-control input-lg',
    ]
); ?>

<div id="container-outbound-process-form-layout" style="margin-top: 30px;"></div>
<div id="container-outbound-layout" style="margin-top: 30px;"></div>


<script type="text/javascript">

    /*
     * Start if change main client drop-down
     * */
    $('body').on('change','#main-form-client-id',function() {

        console.log('change #main-form-client-id');

        var me = $(this),
            url = me.data('url');

        window.location.href = url+'?id='+me.val();
    });

</script>