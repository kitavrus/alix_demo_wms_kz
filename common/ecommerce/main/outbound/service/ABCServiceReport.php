<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:16
 */

namespace common\ecommerce\main\outbound\service;


use common\ecommerce\main\outbound\models\ABCReportSearch;

class ABCServiceReport
{
    private $search;

    public function __construct($params = [])
    {
        $this->search = new ABCReportSearch();
    }

    public function getSearch() { return $this->search; }
}
