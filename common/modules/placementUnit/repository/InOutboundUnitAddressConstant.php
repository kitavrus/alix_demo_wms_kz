<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 02.10.2017
 * Time: 8:41
 */

namespace common\modules\placementUnit\repository;


class InOutboundUnitAddressConstant
{
    const STATUS_NOT_SET = 0; // Не указан
    const STATUS_FREE = 1; // Короб свободен
    const STATUS_WORK = 2; // Короб в работе. примается в данный момен на приемке, но не размещен
    const STATUS_CLOSE = 3; // Короб размещен. Короб размещен в адрес
}