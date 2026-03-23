<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\modules\codebook\models\Codebook;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\codebook\models\CodebookSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('titles', 'Codebook');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="codebook-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('buttons', 'Add record'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'cod_prefix',
            'name',
            'count_cell',
            'barcode',
            [
                'attribute' => 'base_type',
                'value' => function ($model) {
                        return $model->getBaseTypeValue();
                    },
                'filter' => Html::activeDropDownList($searchModel, 'base_type', Codebook::getBaseTypeArray(), ['class' => 'form-control', 'prompt' => Yii::t('titles', 'Select base type')])
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                        return $model->getStatusValue();
                    },
                'filter' => Html::activeDropDownList($searchModel, 'status', Codebook::getStatusArray(), ['class' => 'form-control', 'prompt' => Yii::t('titles', 'Select status')])
            ],
            // 'created_user_id',
            // 'updated_user_id',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
