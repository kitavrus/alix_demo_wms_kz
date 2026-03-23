<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:16
 */

namespace common\clientObject\main\outbound\service;

use common\clientObject\main\outbound\models\ABCZombieReportSearch;

class ABCZombieServiceReport
{
    private $search;

    public function __construct($params = [])
    {
        $this->search = new ABCZombieReportSearch();
    }

    public function getSearch() { return $this->search; }
}
