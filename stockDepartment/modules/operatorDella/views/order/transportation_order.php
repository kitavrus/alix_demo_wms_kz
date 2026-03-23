<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = Yii::t('frontend/menu', 'Оформить заявку');
?>
<?php $this->params['breadcrumbs'][] = $this->title; ?>

<div class="row col-md-12">
    <div class="transportation_order_form col-md-8">
        <?= $this->render('forms/_transportation_order_form', [
            'model' => $model,
        ]) ?>

    </div>

</div>
