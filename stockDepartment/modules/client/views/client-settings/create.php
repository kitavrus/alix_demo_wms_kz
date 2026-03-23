<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\client\models\ClientSettings */

$this->title = Yii::t('titles', 'Create Client Setting');
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Client Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-settings-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
