<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\modules\leads\models\ExternalClientLead;
use yii\helpers\ArrayHelper;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use app\modules\transportLogistics\transportLogistics;
use yii\widgets\DetailView;
use kartik\grid\EditableColumn;

/* @var $this yii\web\View */

$this->title = Yii::t('client/titles', 'Employee profile');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-profile">
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'first_name',
            'middle_name',
            'last_name',
            'email',
            [
                'label' => Yii::t('client/forms', 'Manager type'),
                'value' => $model->getType(),
            ],
           'created_at:datetime',
           'updated_at:datetime',
        ],
    ]) ?>
</div>
<p>
    <?= Html::a(Yii::t('client/buttons', 'Edit profile'), ['edit'], ['class' => 'btn btn-primary']) ?>
</p>
