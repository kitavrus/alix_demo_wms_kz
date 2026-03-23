<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\stock\models\StockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('stock/titles', 'На склaде сделать браком/не браком');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="search-item-index" xmlns="http://www.w3.org/1999/html">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search-make-defect-filter', ['model' => $searchModel]); ?>

    <?php if (Yii::$app->request->get('StockSearch')): ?>
        <div class="make-defect-buttons" style="margin: 20px 0; display: flex; justify-content: flex-end; gap: 10px;">
            <?= Html::beginForm(['make-defect'], 'post', ['style' => 'display: inline;']) ?>
            <?= Html::hiddenInput('StockSearch[primary_address]', $searchModel->primary_address) ?>
            <?= Html::hiddenInput('StockSearch[secondary_address]', $searchModel->secondary_address) ?>
            <?= Html::submitButton(
                Yii::t('stock/titles', 'Сделать браком'),
                [
                    'class' => 'btn btn-danger',
                    'data-confirm' => Yii::t('stock/titles', 'Вы уверены, что хотите пометить выбранные короба как брак?')
                ]
            ) ?>
            <?= Html::endForm() ?>

            <?= Html::beginForm(['make-not-defect'], 'post', ['style' => 'display: inline;']) ?>
            <?= Html::hiddenInput('StockSearch[primary_address]', $searchModel->primary_address) ?>
            <?= Html::hiddenInput('StockSearch[secondary_address]', $searchModel->secondary_address) ?>
            <?= Html::submitButton(
                Yii::t('stock/titles', 'Сделать не браком'),
                [
                    'class' => 'btn btn-success',
                    'data-confirm' => Yii::t('stock/titles', 'Вы уверены, что хотите пометить выбранные короба как не брак?')
                ]
            ) ?>
            <?= Html::endForm() ?>
        </div>
    <?php endif; ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'label' => Yii::t('stock/forms', 'Primary address'),
                'attribute' => 'primary_address',
            ],
            [
                'label' => Yii::t('stock/forms', 'Secondary address'),
                'attribute' => 'secondary_address',
            ],
            [
                'label' => Yii::t('stock/forms', 'Condition type'),
                'attribute' => 'condition_type',
                'value' => function ($data) use ($conditionTypeArray) {
            return isset($conditionTypeArray[$data['condition_type']]) ? $conditionTypeArray[$data['condition_type']] : '-';
        }
            ],
        ],
    ]);
    ?>
</div>