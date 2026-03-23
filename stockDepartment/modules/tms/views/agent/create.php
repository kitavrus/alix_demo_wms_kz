<?php

use yii\helpers\Html;
use app\modules\transportLogistics\transportLogistics;


/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlAgents */

$this->title = Yii::t('transportLogistics/titles', 'Create Agent');
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Tl Agents'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-agents-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
