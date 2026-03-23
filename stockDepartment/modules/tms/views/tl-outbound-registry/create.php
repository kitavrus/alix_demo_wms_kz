<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlOutboundRegistry */

$this->title = Yii::t('transportLogistics/titles', 'Create Tl Outbound Registry');
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Tl: Outbound Registries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-outbound-registry-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
