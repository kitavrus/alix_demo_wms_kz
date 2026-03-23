<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\agentBilling\models\TlAgentBilling */

$this->title = Yii::t('titles', 'Create Agent Billing');
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Billings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-agent-billing-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
