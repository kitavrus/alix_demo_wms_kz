<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\alix\controllers\outboundSeparator\domain\entities\OutboundSeparatorItems */

$this->title = 'Create Outbound Separator Items';
$this->params['breadcrumbs'][] = ['label' => 'Outbound Separator Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outbound-separator-items-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
