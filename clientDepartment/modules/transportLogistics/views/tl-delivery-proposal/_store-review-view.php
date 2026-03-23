<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use common\modules\transportLogistics\components\TLHelper;

?>
<h1 ><?= Html::encode(Yii::t('transportLogistics/titles', 'Store Review')) ?></h1>
<?php if($storeReviewButton1){?>
    <p>
        <?= Html::button(Yii::t('buttons', 'Edit'), ['class' => 'btn btn-primary', 'id' => 'update-review-bt', 'data-id' => $model->id]) ?>
    </p>
<?php } ?>
<div class="store-reviews-view">
    <?= DetailView::widget([

        'model' => $model,

        'attributes' => [

            [
                'attribute'=>'delivery_datetime',
                'format' => 'datetime'

            ],

            [
                'attribute' => 'tl_delivery_proposal_id',
                'value'=> TLHelper::getProposalLabel($model->client_id, $model->tl_delivery_proposal_id),
                'displayOnly'=>true,

            ],
            'number_of_places',
            [
                'attribute' => 'rate',
                'type'=>DetailView::INPUT_RATING,
                'widgetOptions'=>[
                    'pluginOptions'=>['step' => 1],
                ],

            ],
            [
                'attribute' => 'comment',
                'type'=>DetailView::INPUT_TEXTAREA,
                'row' =>6,

            ],
        ],
    ]) ?>
</div>