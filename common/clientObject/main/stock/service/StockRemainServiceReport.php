<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:16
 */

namespace common\clientObject\main\stock\service;


use common\clientObject\main\stock\models\StockDamageRemainsSearch;

class StockRemainServiceReport
{
    private $search;

    public function __construct($params = [])
    {
        $this->search = new StockDamageRemainsSearch();
    }

    public function getSearch() { return $this->search; }
}
