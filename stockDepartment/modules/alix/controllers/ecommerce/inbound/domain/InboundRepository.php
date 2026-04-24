<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\inbound\domain;

use common\ecommerce\entities\EcommerceInbound;
use common\ecommerce\entities\EcommerceInboundItem;
use stockDepartment\modules\alix\controllers\ecommerce\inbound\domain\InboundDataMatrix;

class InboundRepository
{
    public function getClientID()
    {
        return 103;
    }

    public function isExtraBarcodeInOrder($productBarcode, $inboundOrderID)
    {
        return EcommerceInboundItem::find()
            ->andWhere([
                'inbound_id' => $inboundOrderID,
                'product_barcode' => $productBarcode,
            ])
            ->andWhere('product_expected_qty = product_accepted_qty AND product_expected_qty != 0')
            ->exists();
    }

    public function isNotAvailableDataMatrix($inboundId, $productBarcode, $dataMatrix)
    {
        return !InboundDataMatrix::find()
            ->andWhere([
                "inbound_id" => $inboundId,
                "product_barcode" => $productBarcode,
                "data_matrix_code" => $dataMatrix,
                "status" => InboundDataMatrix::NOT_SCANNED,
            ])
            ->exists();
    }

    public function getAvailableDataMatrix($inboundId, $productBarcode, $dataMatrix)
    {
        return InboundDataMatrix::find()
            ->andWhere([
                "inbound_id" => $inboundId,
                "product_barcode" => $productBarcode,
                "data_matrix_code" => $dataMatrix,
                "status" => InboundDataMatrix::NOT_SCANNED,
            ])
            ->one();
    }

    public function setToNotScannedDataMatrix($dataMatrixIds)
    {
        return InboundDataMatrix::updateAll(
            ["status" => InboundDataMatrix::NOT_SCANNED],
            ["id" => $dataMatrixIds]
        );
    }

    /**
     * @return array|EcommerceInbound|\yii\db\ActiveRecord
     */
    public function getOrder($id)
    {
        return EcommerceInbound::find()
            ->andWhere([
                "id" => $id,
                "client_id" => $this->getClientID(),
            ])
            ->one();
    }
}
