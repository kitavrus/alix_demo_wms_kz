<?php
/**
 * Список Kaspi-заказов, для которых ещё не скачана PDF-этикетка.
 * Используется через actionShowKaspiWaitingLabel.
 *
 * @var array $orders Массив выборки из OutboundListService::kaspiOrdersWaitingLabel()
 */

use yii\helpers\Html;
use yii\helpers\Url;
use common\ecommerce\constants\OutboundStatus;
?>
<h1>Ждут Kaspi-этикетку <span class="badge"><?= count($orders) ?></span></h1>

<?php if (empty($orders)): ?>
    <div class="alert alert-success">
        Сейчас нет упакованных Kaspi-заказов без этикетки.
    </div>
<?php else: ?>
    <p>Kaspi отдаёт PDF не сразу после «Упакован». Открой ссылку «Скачать этикетку» — система заберёт PDF и уберёт заказ из этого списка.</p>
    <table class="table table-bordered">
        <thead>
            <tr>
                <td>#</td>
                <td>Номер заказа</td>
                <td>Kaspi ID</td>
                <td>Курьерка</td>
                <td>Статус</td>
                <td>Упакован</td>
                <td>Этикетка</td>
            </tr>
        </thead>
        <?php $asDatetimeFormat = 'php:d.m.Y H:i'; ?>
        <?php $total = count($orders); ?>
        <?php foreach ($orders as $i => $order): ?>
            <tr class="alert-warning">
                <td><?= $total - $i ?></td>
                <td><?= Html::encode($order['order_number']) ?></td>
                <td><?= Html::encode($order['external_order_number']) ?></td>
                <td><?= Html::encode($order['client_CargoCompany']) ?></td>
                <td><?= OutboundStatus::getValue($order['status']) ?></td>
                <td>
                    <?= !empty($order['packing_date'])
                        ? Yii::$app->formatter->asDatetime($order['packing_date'], $asDatetimeFormat)
                        : '—' ?>
                </td>
                <td>
                    <?= Html::a(
                        'Скачать этикетку',
                        Url::toRoute([
                            '/alix/ecommerce/outbound/scanning/kaspi-label',
                            'orderNumber' => $order['order_number'],
                        ]),
                        ['class' => 'btn btn-success btn-sm', 'target' => '_blank']
                    ) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
