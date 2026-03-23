<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\store\models\Store */

$this->title = Yii::t('titles', 'Create Store');
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Stores'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="store-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
