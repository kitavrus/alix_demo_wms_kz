<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\service;

use common\ecommerce\entities\EcommerceStock;

class StockRepository
{
    public function getClientID()
    {
        return 103;
    }

    /**
     * Кладём в EcommerceStock код дата-матрицы. В ecommerce_stock нет отдельного
     * id дата-матрицы (legacy-поле inbound_datamatrix_id), он не нужен — связь
     * остаётся в таблице inbound_data_matrix.
     */
    public function setInboundDataMatrixId($stockId, $datamatrixId, $datamatrix)
    {
        return EcommerceStock::updateAll(
            ['product_qrcode' => $datamatrix],
            ['id' => $stockId]
        );
    }

    public function checkExistDataMatrixByStockId($stockId, $datamatrixId, $datamatrix)
    {
        return EcommerceStock::find()->andWhere(['product_qrcode' => $datamatrix])->exists();
    }

    public function getDataForInboundAPI($inboundOrderId)
    {
        return EcommerceStock::find()
            ->select("product_barcode, product_model, product_sku, GROUP_CONCAT(product_qrcode ORDER BY product_qrcode SEPARATOR '|') as qrcode, count(product_barcode) as productQty")
            ->andWhere(['inbound_id' => $inboundOrderId])
            ->groupBy("product_barcode")
            ->asArray()
            ->all();
    }

    public function getDataForInboundAPIV2($inboundOrderId)
    {
        return EcommerceStock::find()
            ->select("product_sku, count(product_sku) as productQty")
            ->andWhere(['inbound_id' => $inboundOrderId])
            ->groupBy("product_sku")
            ->asArray()
            ->all();
    }

    public function getDataForInboundReturnAPI($inboundOrderId)
    {
        return EcommerceStock::find()
            ->select("product_sku, count(product_barcode) as productQty")
            ->andWhere(['inbound_id' => $inboundOrderId])
            ->groupBy("product_sku")
            ->asArray()
            ->all();
    }

    public function getDataForOutboundAPI($outboundOrderId)
    {
        return EcommerceStock::find()
            ->select("outbound_box AS box_barcode, product_sku, count(product_barcode) as productQty")
            ->andWhere([
                'outbound_id' => $outboundOrderId,
                'status' => EcommerceStock::STATUS_OUTBOUND_SCANNED,
            ])
            ->groupBy("product_sku, outbound_box")
            ->asArray()
            ->all();
    }
}
