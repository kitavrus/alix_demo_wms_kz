<?php

use yii\helpers\Html;
use app\modules\transportLogistics\transportLogistics;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlAgents */

$this->title = Yii::t('transportLogistics/titles', 'Update Agent: ') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Tl Agents'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('transportLogistics/titles', 'Update');
?>
<div class="tl-agents-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
