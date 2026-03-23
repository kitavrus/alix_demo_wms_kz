<?php
$this->title = Yii::t('client/titles', 'Edit Order №' ). $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('client/titles', 'My orders'), 'url' => ['my-orders']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="my-orders-view row col-md-8">
    <?= $this->render('forms/_delivery_proposal_edit_form', [
        'model' => $model,
    ]) ?>
</div>
