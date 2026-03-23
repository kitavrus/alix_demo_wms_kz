<?php

use yii\helpers\Html;
use app\modules\city\city;


/* @var $this yii\web\View */
/* @var $model common\modules\city\models\Country */

$this->title = city::t('titles', 'Create Country');
$this->params['breadcrumbs'][] = ['label' => city::t('titles', 'Countries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="country-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
