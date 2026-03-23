<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:16
 */

namespace common\ecommerce\main\inbound\service;


use common\ecommerce\main\inbound\models\InboundOrderSearch;

class InboundServiceReport
{
    private $search;

    public function __construct($params = [])
    {
        $this->search = new InboundOrderSearch();
    }

    public function getSearch() { return $this->search; }
}
