<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\store\models\StoreReviews */

$this->title = Yii::t('forms', 'Create {modelClass}', [
    'modelClass' => 'Store Reviews',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms', 'Store Reviews'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="store-reviews-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
