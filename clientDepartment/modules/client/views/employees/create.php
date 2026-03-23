<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\client\models\ClientEmployees */

$this->title = Yii::t('titles', 'Create Employee');
//$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Client manager'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-managers-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'storeList' => $storeList,
//        'store_id' => $store_id,
    ]) ?>

</div>
