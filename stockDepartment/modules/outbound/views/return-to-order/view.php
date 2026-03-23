<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use common\modules\transportLogistics\components\TLHelper;
use common\helpers\iHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\stock\models\Stock */
/* @var $boxBarcode string */
/* @var $orderNumber string */
/* @var $boxItemsProvider yii\data\ArrayDataProvider */

$this->title = Yii::t('outbound/titles', 'Короб: ') . $boxBarcode;
$this->params['breadcrumbs'][] = ['label' => 'Вернуть в заказ / Убрать из заказа', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h2><?= Html::encode($this->title) ?></h2>

<?= GridView::widget([
    'dataProvider' => $boxItemsProvider,
    'columns' => [
        [
            'label' => Yii::t('stock/forms', 'Product barcode'),
            'value' => function ($row) {
                return $row['stock']->product_barcode;
            },
        ],
        [
            'label' => Yii::t('outbound/forms', 'Order') . ': ' . $orderNumber,
            'format' => 'raw',
            'value' => function ($row) {
                if ($row['is_in_box']) {
                    $url = Url::to(['return-to-stock', 'id' => $row['stock']->id]);
                    return Html::button(
                        Yii::t('outbound/forms', 'Вернуть на склад'),
                        ['class' => 'btn btn-xs btn-danger js-confirm-action', 'data-url' => $url]
                    );
                }
                $url = Url::to(['return-to-order', 'id' => $row['stock']->id]);
                return Html::button(
                    Yii::t('outbound/forms', 'Вернуть в заказ'),
                    ['class' => 'btn btn-xs btn-success js-confirm-action', 'data-url' => $url]
                );
            },
        ],
        [
            'label' => Yii::t('stock/forms', 'Адрес короба'),
            'value' => function ($row) {
                return $row['stock']->inventory_secondary_address;
            },
        ],
    ],
]); ?>

<?php Modal::begin([
    'id' => 'confirm-action-modal',
    'header' => '<h4 class="modal-title">Подтверждение</h4>',
    'footer' => Html::button('Отмена', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']),
]); ?>
<p>Введите <strong>да</strong> для подтверждения и нажмите Enter.</p>
<?= Html::input('text', 'confirm_action_input', '', [
    'id' => 'confirm-action-input',
    'class' => 'form-control',
    'placeholder' => 'да',
    'autocomplete' => 'off',
]); ?>
<?php Modal::end(); ?>

<?php
$js = <<<'JS'
(function() {
    var modal = $('#confirm-action-modal');
    var input = $('#confirm-action-input');
    var actionUrl = '';

    function doConfirm() {
        var val = input.val().trim().toLowerCase();
        if (val === 'да' || val === 'da') {
            modal.modal('hide');
            window.location.href = actionUrl;
        } else {
            alert('Введите "да" для подтверждения.');
            input.focus();
        }
    }

    $(document).on('click', '.js-confirm-action', function() {
        actionUrl = $(this).data('url');
        input.val('');
        modal.modal('show');
        setTimeout(function() { input.focus(); }, 500);
    });

    input.on('keydown', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            doConfirm();
        }
    });

    modal.on('hidden.bs.modal', function() {
        input.val('');
        actionUrl = '';
    });
})();
JS;
$this->registerJs($js);
?>