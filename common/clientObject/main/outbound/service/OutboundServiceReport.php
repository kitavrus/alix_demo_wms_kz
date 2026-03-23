<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:16
 */

namespace common\clientObject\main\outbound\service;


use common\clientObject\main\outbound\models\OutboundOrderSearch;

class OutboundServiceReport
{
    private $search;

    public function __construct($params = [])
    {
        $this->search = new OutboundOrderSearch();
    }

    public function getSearch() { return $this->search; }
}
