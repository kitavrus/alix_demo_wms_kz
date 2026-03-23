<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\client\models\ClientGroup */

$this->title = Yii::t('app', 'Create Client Group');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Client Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
