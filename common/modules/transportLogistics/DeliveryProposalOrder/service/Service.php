<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 22.09.2017
 * Time: 8:40
 */

namespace common\modules\transportLogistics\DeliveryProposalOrder\service;


class Service
{
    private $repository;
    private $validate;
    /**
     * Service constructor.
     */
    public function __construct()
    {
        $this->repository = new \common\modules\transportLogistics\DeliveryProposalOrder\repository\Repository();
        $this->validate = new \common\modules\transportLogistics\DeliveryProposalOrder\Validate\Validate( $this->repository);
    }

    public function create(\common\modules\transportLogistics\DeliveryProposalOrder\dto\Create $data) {
        $this->validate->canCreate($data);
        $this->repository->create($data);
    }
    public function makeDtoForCrate(){}

    public function update(){}
    public function createOrUpdate(){}
    public function findOrCreate(){}
    public function delete(){}
}