<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:16
 */

namespace common\ecommerce\main\outbound\service;

use common\ecommerce\main\outbound\models\ABCZombieReportSearch;

class ABCZombieServiceReport
{
    private $search;

    public function __construct($params = [])
    {
        $this->search = new ABCZombieReportSearch();
    }

    public function getSearch() { return $this->search; }
}
