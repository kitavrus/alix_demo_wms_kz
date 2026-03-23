<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\codebook\models\Codebook */

$this->title = Yii::t('titles', 'Update record: ') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Codebook'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('titles', 'Update');
?>
<div class="codebook-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
