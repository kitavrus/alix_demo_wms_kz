<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\intermode\controllers\outboundSeparator\domain\entities\OutboundSeparator */

$this->title = 'Create Outbound Separator';
$this->params['breadcrumbs'][] = ['label' => 'Outbound Separators', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outbound-separator-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
