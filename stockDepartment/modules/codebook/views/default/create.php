<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\codebook\models\Codebook */

$this->title = Yii::t('titles', 'Add code');
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Codebook'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="codebook-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
