<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 10.02.15
 * Time: 14:58
 */
?>
<?= $this->render('_search', ['model' => $searchModel,'clientsArray' => $clientsArray]); ?>
<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'barcode',
        [
            'attribute'=>'employee_id',
            'value'=>function($data){
                return $data->showEmployeeName();
            },
        ],

        [
            'attribute'=>'qty_lot',
            'label'=>'Кол-во лотов',
            'value'=>function($data){
                return $data->showCountLot();
            },
        ],
        [
            'label' =>'Разница',
            'attribute'=>'diff_time_find_products',
            'format'=>'raw',
            'value' => function($data) {
                return Yii::$app->formatter->asDuration($data->showDiffKPIBeginEndDateTime());
            },
            'contentOptions'=>function($data) {
                return $data->showDiffKPIBeginEndDateTime() < 0 ?  ['style'=>'color:green;'] :  ['style'=>'color:red;'];
            }
        ],
        [
            'label' =>'Разница %',
            'attribute'=>'diff_time_find_products',
            'format'=>'raw',
            'value' => function($data) {
                return Yii::$app->formatter->asDecimal($data->showPercentDiffKPIBeginEndDateTime(),2);
            },
            'contentOptions'=>function($data) {
                return $data->showDiffKPIBeginEndDateTime() < 0 ?  ['style'=>'color:green;'] : ($data->showDiffKPIBeginEndDateTime() == 0.00 ? [] : ['style'=>'color:red;']);
            }
        ],
        [
            'attribute'=>'time_by_lot',
            'label'=>'Время за 1 лот',
            'value'=>function($data) {

                if($data->kpi_value && $data->showCountLot()) {
                    return Yii::$app->formatter->asDuration($data->kpi_value / $data->showCountLot());
                }
                return '';
            },
        ],
        [
            'label' =>'Время по KPI сборки',
            'attribute'=>'kpi_value',
            'format'=>'raw',
            'value' => function($data) {
                if(!empty($data->kpi_value)) {
                    return Yii::$app->formatter->asDuration($data->kpi_value);
                }
                return 0;
            }
        ],
        [
            'label' =>'Фак-е время сборки',
            'attribute'=>'showDiffBeginEndDateTime',
            'format'=>'raw',
            'value' => function($data) {
                return Yii::$app->formatter->asDuration($data->showDiffRealBeginEndDateTime());
            }
        ],
        [
            'attribute'=>'client_id',
            'value'=>function($data) use ($clientsArray){
                return \yii\helpers\ArrayHelper::getValue($clientsArray,$data->client_id);
            },
        ],
        [
            'attribute'=>'status',
            'value'=>function($data){
                return $data->getStatusValue();
            },
        ],
        [
            'label' =>'Начало сборки',
            'attribute'=>'begin_datetime',
            'format'=>'datetime',
        ],
        [
            'label' =>'Конец сборки',
            'attribute'=>'end_datetime',
            'format'=>'datetime',
        ],
        'created_at:datetime',
    ],
]); ?>

<?= \yii\bootstrap\Html::tag('span',Yii::t('transportLogistics/buttons','Экспорт в Эксель'),['class' => 'btn btn-success','id'=>'kpi-employee-picking-export-btn', 'data-url'=>'/employee/kpi/picking-outbound-export-to-excel']) ?>

<script type="text/javascript">
    $(function(){

        $('#kpi-employee-picking-export-btn').on('click',function() {
            console.info($('#picklist-search-form').find('.form-control').serialize());

            window.location.href = $(this).data('url')+'?'+$('#picklist-search-form').find('.form-control').serialize();
        });
    });
</script>