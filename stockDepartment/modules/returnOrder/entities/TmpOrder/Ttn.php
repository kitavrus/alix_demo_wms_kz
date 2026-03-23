<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 05.04.2017
 * Time: 12:02
 */

namespace stockDepartment\modules\returnOrder\entities\TmpOrder;


use common\modules\transportLogistics\models\TlDeliveryProposal;
use yii\web\NotFoundHttpException;

class Ttn
{
    private $repository;

    /**
     * ttn constructor.
     */
    public function __construct() // ?
    {
        $this->repository = new TlDeliveryProposal(); // ?
    }

    public static function getQtyPlacesById($id)
    {
        return self::findById($id)->number_places;
    }



    public static function findById($id)
    {
        if (($model = TlDeliveryProposal::find()->andWhere(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('DeliveryProposal does not exist. Ttn entity');
        }
    }
}