<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TlAgentsBookkeeper */

$this->title = Yii::t('app', 'Create Tl Agents Bookkeeper');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tl Agents Bookkeepers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-agents-bookkeeper-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
