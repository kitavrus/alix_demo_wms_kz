<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\employees\models\Employees */

$this->title = Yii::t('titles', 'Create Employee');
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Employees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employees-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
