<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = Yii::t('client/titles', 'Order №' ). $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('client/titles', 'My orders'), 'url' => ['my-orders']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="my-orders-view">
    <?php echo Html::beginTag('p'); ?>
    <?php echo Html::tag('h1',$this->title);  ?>
    <?php echo $model->showEditButtonForOperator();  ?>
    <?php echo $model->showDeleteButtonForOperator();  ?>

    <?=  \yii\bootstrap\ButtonDropdown::widget([
        'label' => Yii::t('client/buttons','Print box label'),
        'dropdown' => [
            'items' => [
                ['label' => Yii::t('transportLogistics/buttons','Format A4'), 'url' => ['print-box-label','id'=>$model->id,'type'=>'1']],
                ['label' => Yii::t('transportLogistics/buttons','Format self-adhesive'), 'url' => ['print-box-label','id'=>$model->id,'type'=>'2']],
            ],
        ],
    ]);
    ?>
    <?php
    if ($model->canPrintTtn()) {
        echo Html::a(Yii::t('client/buttons', 'Print TTN'), ['print-ttn', 'id' => $model->id], ['class' => 'btn btn-warning']);
    } ?>

    <?php echo Html::endTag('p');?>

    <?php
    echo  DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => Yii::t('client/forms', 'Customer name'),
                'value' => \yii\helpers\ArrayHelper::getValue($model->client,'legal_company_name'),
            ],
            [
                'label' => Yii::t('operator/forms', 'SENDER_CONTACT'),
                'value' =>  $model->sender_contact,
            ],
            [
                'label' => Yii::t('operator/forms', 'RECIPIENT_CONTACT'),
                'value' =>  $model->recipient_contact,
            ],

            [
                'label' => Yii::t('client/forms', 'Delivery method'),
                'value' => $model->getDeliveryMethod(),
            ],
            [
                'label' => Yii::t('operator/forms', 'WHO_PAYS'),
                'value' => $model->getTransportWhoPays(),
            ],
            [
                'label' => Yii::t('client/forms', 'Created At'),
                'value' => Yii::$app->formatter->asDatetime($model->created_at),
            ],
            [
                'label' => Yii::t('client/forms', 'Status'),
                'value' => $model->getStatusForclient(),
            ],
            [
                'label' => Yii::t('client/forms', 'City from'),
                'value' => isset($storeArray[$model->route_from]) ? $storeArray[$model->route_from] : Yii::t('titles', 'Not set'),
            ],
            [
                'label' => Yii::t('client/forms', 'City to'),
                'value' => isset($storeArray[$model->route_to]) ? $storeArray[$model->route_to] : Yii::t('titles', 'Not set'),
            ],
            [
                'label' => Yii::t('client/forms', 'Volume'),
                'value' => $model->mc,
            ],
            [
                'label' => Yii::t('client/forms', 'Volume actual'),
                'value' => $model->mc_actual,
            ],
            [
                'label' => Yii::t('client/forms', 'Weight'),
                'value' => $model->kg,
            ],
            [
                'label' => Yii::t('client/forms', 'Weight actual'),
                'value' => $model->kg_actual,
            ],
            [
                'label' => Yii::t('client/forms', 'Places'),
                'value' => $model->number_places,
            ],
            [
                'label' => Yii::t('client/forms', 'Places actual'),
                'value' => $model->number_places_actual,
            ],
            [
                'label' => Yii::t('client/forms', 'Shipment description'),
                'value' => $model->comment,
            ],
            [
                'label' => Yii::t('operator/forms', 'TYPE_LOADING'),
                'value' => $model->getTransportTypeLoadingValue(),
            ],
            [
                'label' => Yii::t('client/forms', 'Declared value'),
                'format' => 'html',
                'value' => Yii::$app->formatter->asCurrency($model->declared_value),
            ],

            [
                'label' => Yii::t('client/forms', 'Total price'),
                'value' => $model->showPriceForOperator(),
            ],
        ],
    ]);
?>
</div>