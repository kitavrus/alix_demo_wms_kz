<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\events;

use yii\base\Event;

/**
*
 */
class DpEvent extends Event
{
    /**
     * @var \common\modules\transportLogistics\models\TlDeliveryProposal  Delivery proposal ID
     */
    public $deliveryProposalId = null;

    /**
     * @var \common\modules\transportLogistics\models\TlDeliveryRoutes  Delivery proposal route ID
     */
    public $deliveryRouteId = null;

}
