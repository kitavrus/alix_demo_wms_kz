<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\modules\transportLogistics\components\TLHelper;
use common\helpers\iHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\billing\models\TlDeliveryProposalBilling */

$this->title = Yii::t('outbound/titles', 'Order №').$checkBox->inventory_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('outbound/titles', 'Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<?= DetailView::widget([
    'model' => $checkBox,
    'attributes' => [
        'inventory_id',
        'box_barcode',
        'place_address',
        'expected_qty',
        'scanned_qty',
        'created_at:datetime',
        'updated_at:datetime',
    ],
]) ?>
<?= $productsInBox; ?>