<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 03.07.2017
 * Time: 9:33
 */

namespace stockDepartment\modules\wms\managers\miele;


class Constants
{
    const STATUS_PROBLEM = 0; // 'проблема',
    const STATUS_NEW = 1; //'новая'
    const STATUS_IN_WORKING = 2; //'в работе'
    const STATUS_RESERVED = 3; //'подобрана'
    const STATUS_COMPLETE = 4; //'исполнена'
    const STATUS_CANCEL = 5; //'отменена'

    private $status = [
        self::STATUS_PROBLEM => 'проблема', // (не хватило остатков, не найден запрошенный фн или кн и проч.)
        self::STATUS_NEW => 'новая',
        self::STATUS_IN_WORKING => 'в работе', // (по заявка начата работа, изменение не возможно)
        self::STATUS_RESERVED => 'подобрана', // (т.е. уже определены участвующие в заявке фн (отсканированы на складе) и гтд (по данным системы ЛО))
        self::STATUS_COMPLETE => 'исполнена',
        self::STATUS_CANCEL => 'отменена',
    ];

    const ZONE_CATEGORY_A = 0;  // товары категории А
    const ZONE_CATEGORY_B = 1;  // товары категории Б
    const ZONE_CATEGORY_VV = 2; // товары  категории ВB
    const ZONE_RETURN = 3;      // возвраты
    const ZONE_FUNDS = 4;       // фонды
    const ZONE_UNADAPTED = 5;   // неадаптированные

    private $zone = [
        self::ZONE_CATEGORY_A => 'товары категории А',
        self::ZONE_CATEGORY_B => 'товары категории Б',
        self::ZONE_CATEGORY_VV => 'товары  категории ВB',
        self::ZONE_RETURN => 'возвраты',
        self::ZONE_FUNDS => 'фонды',
        self::ZONE_UNADAPTED => 'неадаптированные',
    ];

    const ALLOCATION_BY_FAB_KEY = '-allocation-by-fab-';

    private $soapErrors = [
        "001" => [
            'cod' => '001',
            'message' => 'Заявка {СтрочноеПредставлениеДокументаПрообраза} не может быть принята! Статус в системе ЛО : 001',
        ],
        "002" => [
            'cod' => '002',
            'message' => 'Заявка {СтрочноеПредставлениеДокументаПрообраза} не может быть принята! Статус в системе ЛО : 002',
        ],
    ];

    public function getClientStatus($status) {
        return isset($this->status[$status]) ? $this->status[$status] : 'не найден';
    }

    public function getClientZone($zone) {
        return isset($this->zone[$zone]) ? $this->zone[$zone] : 'не найден';
    }
}