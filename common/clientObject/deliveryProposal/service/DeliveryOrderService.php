<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:16
 */

namespace common\clientObject\deliveryProposal\service;


use common\clientObject\deliveryProposal\models\DeliveryProposalSearch;

class DeliveryOrderService
{
    private $search;

    public function __construct($params = [])
    {
        $this->search = new DeliveryProposalSearch();
    }

    public function getSearch() { return $this->search; }
}
