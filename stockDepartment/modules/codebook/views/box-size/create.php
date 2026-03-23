<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\codebook\models\BoxSize */

$this->title = Yii::t('forms', 'Create Box Size');
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms', 'Box Sizes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-size-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
