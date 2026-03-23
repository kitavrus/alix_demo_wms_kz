<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\client\models\Client */

$this->title = Yii::t('titles', 'Profile update: ') . $model->username;
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>
<div class="client-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
