<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\modules\product\models\Product;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\product\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('titles', 'Products');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('buttons', 'Create Product'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
			'name',
			'model',
			'color',
			'size',
			'category',
			'gender',
			'field_extra1',
			'field_extra2',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                        return $model->getStatus();
                    },
                'filter' => Html::activeDropDownList($searchModel, 'status', Product::getStatusArray(), ['class' => 'form-control', 'prompt' => Yii::t('forms', 'Status')])
            ],
			['class' => 'yii\grid\ActionColumn',
				'template'=>'{update} {view}',
			]
        ],
    ]); ?>

</div>
