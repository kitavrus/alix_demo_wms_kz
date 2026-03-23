<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 18.12.2015
 * Time: 12:38
 */
?>
<?= $this->render('_search', ['model' => $searchModel]); ?>
<?php
echo "<br />";
echo "<br />";
echo "<h1>".Yii::t('kpi-delivered/titles','In terms')."</h1>";
echo "<br />";

echo \yii2mod\c3\chart\Chart::widget([
    'options' => [
        'id' => 'bar_chart1',

    ],
    'clientOptions' => [
        'legend' => [
            'item' => [
                'onclick' => 'NQS:function (id) { var shipped_datetime = $(\'#tldeliveryproposalformsearch-shipped_datetime\').val(); var country_id = $(\'#tldeliveryproposalformsearch-country_id\').val(); var href = \'/warehouseDistribution/tupperware/chart-delivery/export-to-excel-kpi-delivered?TlDeliveryProposalSearch[shipped_datetime]=\'+shipped_datetime+\'&TlDeliveryProposalSearch[country_id]=\'+country_id+\'&TlDeliveryProposalSearch[route_from]='.$routeFromDP.'&filter=\'+id; window.location.href = href; console.info(href); }NQE:',
            ]
        ],
        'data' => [
//
            'x' => 'x',
            'columns' => $columnsByCity,
            'type' => 'bar',
            'groups' => $groupsByCity,
            'names' => $namesByCity
        ],
        'size'=> [
//            'height'=>2500,
            //'width'=>3500,
        ],
        'axis' => [
//            'rotated'=> true,
            'x' => [
                'type' => 'category' // this needed to load string x value
            ]
        ]
    ]
]);

echo "<br />";
echo "<br />";
echo "<h1>".Yii::t('kpi-delivered/titles','By M3')."</h1>";
echo "<br />";

echo \yii2mod\c3\chart\Chart::widget([
    'options' => [
        'id' => 'bar_chart_m3'
    ],
    'clientOptions' => [
        'data' => [
            'x' => 'x',
            'columns' => $columnsM3,
            'type' => 'bar',
            'labels' => true
        ],
        'axis' => [
            'x' => [
                'type' => 'category'
            ]
        ]
    ]
]);

echo "<br />";
echo "<br />";
echo "<h1>".Yii::t('kpi-delivered/titles','By Places')."</h1>";
echo "<br />";

echo \yii2mod\c3\chart\Chart::widget([
    'options' => [
        'id' => 'bar_chart_np'
    ],
    'clientOptions' => [
        'data' => [
            'x' => 'x',
            'columns' => $columnsNP,
            'type' => 'bar',
            'labels' => true
        ],
        'axis' => [
            'x' => [
                'type' => 'category' // this needed to load string x value
            ]
        ]
    ]
]);
?>