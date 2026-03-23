<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\stock\models\Stock */

$this->title = Yii::t('froms', 'Create {modelClass}', [
    'modelClass' => 'Stock',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('froms', 'Stocks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
