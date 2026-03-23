<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 21.11.2016
 * Time: 10:01
 */

namespace stockDepartment\modules\wms\managers\defacto;


use common\modules\stock\models\ConsignmentUniversal;
use stockDepartment\modules\wms\managers\defacto\ConsignmentUniversalStatus;

interface ConsignmentUniversalRepositoryInterface {
    public function __construct($id);
    public function saveStatus(ConsignmentUniversalStatus $status);
}

class ConsignmentUniversalRepository implements  ConsignmentUniversalRepositoryInterface
{
    protected $id;

    public function __construct($id)
    {
        if( ConsignmentUniversal::find()->andWhere(['id'=>$id])->exists())
            $this->id = $id;
        else
            throw new \InvalidArgumentException();
    }

    public function saveStatus(ConsignmentUniversalStatus $status)
    {
        ConsignmentUniversal::updateAll([
            'status'=>$status->getStatus(),
        ],[
            'id'=>$this->id,
            'order_type'=>$status->getType()
        ]);
    }
}