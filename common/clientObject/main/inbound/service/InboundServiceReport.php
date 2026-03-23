<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:16
 */

namespace common\clientObject\main\inbound\service;


use common\clientObject\main\inbound\models\InboundOrderSearch;

class InboundServiceReport
{
    private $search;

    public function __construct($params = [])
    {
        $this->search = new InboundOrderSearch();
    }

    public function getSearch() { return $this->search; }
}
