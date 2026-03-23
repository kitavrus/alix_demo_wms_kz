<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.06.2017
 * Time: 16:32
 */

namespace stockDepartment\modules\sheetShipment\entities;


abstract class Id
{
    protected $id;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function isEqualTo(self $other)
    {
        return $this->getId() === $other->getId();
    }
}