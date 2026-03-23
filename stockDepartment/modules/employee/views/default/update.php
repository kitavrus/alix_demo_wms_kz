<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\employees\models\Employees */

$this->title = Yii::t('titles', 'Update Employee: ') . ' ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Employees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('titles', 'Update');
?>
<div class="employees-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
