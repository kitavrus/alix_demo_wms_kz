<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\DataColumn;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('titles', 'View changes for record');
?>
<div class="audit-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'date_created:datetime',
            [
                'class' => DataColumn::className(),
                'attribute' => 'created_by',
                'value' => function($data){
                        if(is_object($data->createdBy)){
                                return $data->createdBy->getUserTitle();
                        }
                    return 'Не задано';
                },
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'field_name',
                'value' => function($data){
                    if($data->getFieldLabel()){
                        return $data->getFieldLabel();
                    }
                    return $data->field_name;
                },
            ],
            'before_value_text',
            'after_value_text',
        ],
    ]); ?>

</div>

