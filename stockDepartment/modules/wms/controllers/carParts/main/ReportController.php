<?php

namespace app\modules\wms\controllers\carParts\main;

use common\clientObject\constants\Constants;
use common\clientObject\main\inbound\service\InboundServiceReport;
use common\clientObject\deliveryProposal\service\DeliveryOrderService;
use common\clientObject\main\outbound\service\OutboundServiceReport;
use common\modules\city\models\RouteDirections;
use common\modules\client\models\Client;
//use common\modules\outbound\models\OutboundOrderItem;
use common\modules\stock\models\Stock;
use stockDepartment\components\Controller;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use common\modules\store\models\Store;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use common\modules\stock\service\Service;

class ReportController extends Controller
{
    public function actionInbound()
    {
        $search = new InboundServiceReport();
        $activeDataProvider = $search->getSearch()->search(Yii::$app->request->queryParams);

        $clientsArray = Client::getActiveByIDs(Constants::getCarPartClientIDs());

        return $this->render('inbound/index', [
            'activeDataProvider' => $activeDataProvider,
            'searchModel' => $search->getSearch(),
            'clientsArray' => $clientsArray,
        ]);
    }

    public function actionInboundToExcel()
    {
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-' . date('d.m.Y'));

        $i = 1;
        $activeSheet->setCellValue('A' . $i, 'Клиент'); // +
        $activeSheet->setCellValue('B' . $i, 'Номер заказа'); // +
        $activeSheet->setCellValue('C' . $i, 'Предполагаемое кол-во'); // +
        $activeSheet->setCellValue('D' . $i, 'Отсканированное кол-во'); // +
        $activeSheet->setCellValue('E' . $i, 'Дата создания заказа'); // +
        $activeSheet->setCellValue('F' . $i, 'Дата подтверждения'); // +
        $activeSheet->setCellValue('G' . $i, 'Статус'); // +

        $search = new InboundServiceReport();
        $activeDataProvider = $search->getSearch()->search(Yii::$app->request->queryParams);
        $activeDataProvider->pagination = false;
        $dps = $activeDataProvider->getModels();

        foreach ($dps as $model) {
            $i++;
            $clientTitle = ArrayHelper::getValue($model->client, 'legal_company_name');

            $activeSheet->setCellValue('A' . $i, $clientTitle);
            $activeSheet->setCellValue('B' . $i, $model->order_number);
            $activeSheet->setCellValue('C' . $i, $model->expected_qty);
            $activeSheet->setCellValue('D' . $i, $model->accepted_qty);

            $activeSheet->setCellValue('E' . $i, !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at) : '-');
            $activeSheet->setCellValue('F' . $i, !empty ($model->date_confirm) ? Yii::$app->formatter->asDatetime($model->date_confirm) : '-');

            $activeSheet->setCellValue('G' . $i, $model->getStatusValue());
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="inbound-orders-report-car-parts.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }
	
	

    public function actionInboundPutAwayToExcel()
    { // /wms/carParts/main/report/inbound-put-away-to-excel
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-' . date('d.m.Y'));

        $i = 1;
        $activeSheet->setCellValueExplicit('A' . $i, 'Адрес', \PHPExcel_Cell_DataType::TYPE_STRING); // +
        $activeSheet->setCellValueExplicit('B' . $i, 'Короб', \PHPExcel_Cell_DataType::TYPE_STRING); // +
        $activeSheet->setCellValueExplicit('C' . $i, 'Товар', \PHPExcel_Cell_DataType::TYPE_STRING); // +
        $activeSheet->setCellValueExplicit('D' . $i, 'Кол-во', \PHPExcel_Cell_DataType::TYPE_STRING); // +

        $search = new InboundServiceReport();
        $activeDataProvider = $search->getSearch()->search(Yii::$app->request->queryParams);
        $activeDataProvider->pagination = false;
        $dps = $activeDataProvider->getModels();//->asArray();


        $stockService = new Service();
        foreach ($dps as $model) {
           $stockList = $stockService->inboundPutAway($model->id);
            foreach ($stockList as $stock) {
                $i++;
                $activeSheet->setCellValue('A' . $i, $stock['secondary_address']);
                $activeSheet->setCellValue('B' . $i, $stock['primary_address']);
                $activeSheet->setCellValue('C' . $i, $stock['product_barcode']);
                $activeSheet->setCellValue('D' . $i, $stock['qty']);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="inbound-где-разместили.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }
	

    public function actionInboundBillingToExcel()
    {
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-' . date('d.m.Y'));

        $i = 1;
        $row = 0;
        $activeSheet->setCellValue('A' . $i, 'Клиент'); // +
        $activeSheet->setCellValue('B' . $i, 'Номер заказа'); // +
        $activeSheet->setCellValue('C' . $i, 'Отсканированный артикул'); // +
        $activeSheet->setCellValue('D' . $i, 'Дата создания заказа'); // +
        $activeSheet->setCellValue('E' . $i, 'Дата подтверждения'); // +
        $activeSheet->setCellValue('F' . $i, 'Кол-во'); // +
        $activeSheet->setCellValue('G' . $i, 'Наименование'); // +
        $activeSheet->setCellValue('H' . $i, 'Строка'); // +

        $search = new InboundServiceReport();
        $activeDataProvider = $search->getSearch()->search(Yii::$app->request->queryParams);
        $activeDataProvider->pagination = false;
        $dps = $activeDataProvider->getModels();

        foreach ($dps as $model) {

            $clientTitle = ArrayHelper::getValue($model->client, 'legal_company_name');

            $items = $model->orderItems;

            foreach ($items as $item) {
                if((int)$item->accepted_qty < 1) {
                    continue;
                }
                $i++;
                $row++;
                $activeSheet->setCellValue('A' . $i, $clientTitle);
                $activeSheet->setCellValue('B' . $i, $model->order_number);
                $activeSheet->setCellValue('C' . $i, $item->product_barcode);
                $activeSheet->setCellValue('D' . $i, !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at) : '-');
                $activeSheet->setCellValue('E' . $i, !empty ($model->date_confirm) ? Yii::$app->formatter->asDatetime($model->date_confirm) : '-');
                $activeSheet->setCellValue('F' . $i, $item->accepted_qty);
                $activeSheet->setCellValue('G' . $i, $item->product_name);
                $activeSheet->setCellValue('H' . $i, $row);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="inbound-orders-report-car-parts-billing.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    public function actionInboundDiffToExcel()
    {
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-' . date('d.m.Y'));

        $i = 1;
        $row = 0;
        $activeSheet->setCellValue('A' . $i, 'Номер заказа'); // +
        $activeSheet->setCellValue('B' . $i, 'Название товара'); // +
        $activeSheet->setCellValue('C' . $i, 'Артикул товара'); // +
        $activeSheet->setCellValue('D' . $i, 'Ожидали'); // +
        $activeSheet->setCellValue('E' . $i, 'Приняли'); // +
        $activeSheet->setCellValue('F' . $i, 'Разница'); // +
        $activeSheet->setCellValue('G' . $i, 'Статус'); // +

        $search = new InboundServiceReport();
        $activeDataProvider = $search->getSearch()->search(Yii::$app->request->queryParams);
        $activeDataProvider->pagination = false;
        $dps = $activeDataProvider->getModels();

        foreach ($dps as $model) {

            $items = $model->orderItems;

            foreach ($items as $item) {
                if((int)$item->accepted_qty == (int)$item->expected_qty) {
                    continue;
                }
                $i++;
                $row++;
                $diff = (int)$item->expected_qty - (int)$item->accepted_qty;
                $activeSheet->setCellValue('A' . $i, $model->order_number);
                $activeSheet->setCellValue('B' . $i, $item->product_name);
                $activeSheet->setCellValue('C' . $i, $item->product_barcode);
                $activeSheet->setCellValue('D' . $i, $item->expected_qty);
                $activeSheet->setCellValue('E' . $i, $item->accepted_qty);
                $activeSheet->setCellValue('F' . $i, $diff);
                $activeSheet->setCellValue('G' . $i, "");

                $productInStockWithAddress = Stock::find()->select('primary_address, secondary_address, count(id) as productQty, status_availability')
                                            ->andWhere(['product_barcode'=> $item->product_barcode,'client_id'=> $model->client_id])
                                            ->groupBy('primary_address, secondary_address, product_barcode, status_availability')
                                            ->asArray()
                                            ->all();

                foreach($productInStockWithAddress as $product) {
                    $i++;
                    $activeSheet->setCellValue('A' . $i, $model->order_number);
                    $activeSheet->setCellValue('B' . $i, $item->product_name);
                    $activeSheet->setCellValue('C' . $i, $item->product_barcode);
                    $activeSheet->setCellValue('D' . $i, $product['secondary_address']);
                    $activeSheet->setCellValue('E' . $i, $product['primary_address']);
                    $activeSheet->setCellValue('F' . $i, $product['productQty']);
                    $activeSheet->setCellValue('G' . $i, (new Stock)->getAvailabilityStatusValue($product['status_availability']));
                }
                $i++;
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="inbound-orders-report-car-parts-diff.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }


    public function actionOutbound()
    {

        $search = new OutboundServiceReport();
        $activeDataProvider = $search->getSearch()->search(Yii::$app->request->queryParams);

        $clientsArray = Client::getActiveByIDs(Constants::getCarPartClientIDs());
        $storesArray = (new \common\modules\store\service\Service())->getStoreCityNameByClientWithPattern(Constants::getCarPartClientIDs());

        return $this->render('outbound/index', [
            'activeDataProvider' => $activeDataProvider,
            'searchModel' => $search->getSearch(),
            'clientsArray' => $clientsArray,
            'storesArray' => $storesArray,
        ]);
    }

    public function actionOutboundToExcel()
    {
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-' . date('d.m.Y'));

        $i = 1;
        $activeSheet->setCellValue('A' . $i, 'Клиент'); // +
        $activeSheet->setCellValue('B' . $i, 'Номер заказа'); // +
        $activeSheet->setCellValue('C' . $i, 'Куда'); // +
        $activeSheet->setCellValue('D' . $i, 'Отсканированное кол-во мест'); // +
        $activeSheet->setCellValue('E' . $i, 'Предполагаемое кол-во'); // +
        $activeSheet->setCellValue('F' . $i, 'Зарезервированое кол-во'); // +
        $activeSheet->setCellValue('G' . $i, 'Отсканированное кол-во'); // +
        $activeSheet->setCellValue('H' . $i, 'Дата регистрации заказа'); // +
        $activeSheet->setCellValue('I' . $i, 'Дата упаковки'); // +
        $activeSheet->setCellValue('J' . $i, 'Дата отгрузки');
        $activeSheet->setCellValue('K' . $i, 'Статус'); // +

        $search = new OutboundServiceReport();
        $activeDataProvider = $search->getSearch()->search(Yii::$app->request->queryParams);
        $activeDataProvider->pagination = false;
        $dps = $activeDataProvider->getModels();

        $storesArray = (new \common\modules\store\service\Service())->getStoreCityNameByClientWithPattern(Constants::getCarPartClientIDs());

        foreach ($dps as $model) {
            $i++;
            $title = ArrayHelper::getValue($storesArray, $model->to_point_id);
            $clientTitle = ArrayHelper::getValue($model->client, 'legal_company_name');

            $activeSheet->setCellValue('A' . $i, $clientTitle);
            $activeSheet->setCellValue('B' . $i, $model->order_number);
            $activeSheet->setCellValue('C' . $i, $title);
            $activeSheet->setCellValue('D' . $i, $model->accepted_number_places_qty);
            $activeSheet->setCellValue('E' . $i, $model->expected_qty);
            $activeSheet->setCellValue('F' . $i, $model->allocated_qty);
            $activeSheet->setCellValue('G' . $i, $model->accepted_qty);

            $activeSheet->setCellValue('H' . $i,
                !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at) : '-');
            $activeSheet->setCellValue('I' . $i,
                !empty ($model->packing_date) ? Yii::$app->formatter->asDatetime($model->packing_date) : '-');
            $activeSheet->setCellValue('J' . $i,
                !empty ($model->date_left_warehouse) ? Yii::$app->formatter->asDatetime($model->date_left_warehouse) : '-');

            $activeSheet->setCellValue('K' . $i, $model->getStatusValue());
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="outbound-orders-report-car-parts.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    public function actionOutboundBillingToExcel()
    {
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-' . date('d.m.Y'));

        $i = 1;
        $row = 0;
        $activeSheet->setCellValue('A' . $i, 'Клиент'); // +
        $activeSheet->setCellValue('B' . $i, 'Номер заказа'); // +
        $activeSheet->setCellValue('C' . $i, 'Куда'); // +
        $activeSheet->setCellValue('D' . $i, 'Отсканированный артикул'); // +
        $activeSheet->setCellValue('E' . $i, 'Дата регистрации заказа'); // +
        $activeSheet->setCellValue('F' . $i, 'Дата упаковки'); // +
        $activeSheet->setCellValue('G' . $i, 'Дата отгрузки');
		$activeSheet->setCellValue('H' . $i, 'Кол-во'); // +
        $activeSheet->setCellValue('I' . $i, 'Наименование'); // +
        $activeSheet->setCellValue('J' . $i, 'Строка'); // +

        $search = new OutboundServiceReport();
        $activeDataProvider = $search->getSearch()->search(Yii::$app->request->queryParams);
        $activeDataProvider->pagination = false;
        $dps = $activeDataProvider->getModels();

        $storesArray = (new \common\modules\store\service\Service())->getStoreCityNameByClientWithPattern(Constants::getCarPartClientIDs());

        foreach ($dps as $model) {

            $title = ArrayHelper::getValue($storesArray, $model->to_point_id);
            $clientTitle = ArrayHelper::getValue($model->client, 'legal_company_name');

            $items = $model->orderItems;

            foreach ($items as $item) {
                if((int)$item->accepted_qty < 1) {
                    continue;
                }
                $i++;
                $row++;
                $activeSheet->setCellValue('A' . $i, $clientTitle);
                $activeSheet->setCellValue('B' . $i, $model->order_number);
                $activeSheet->setCellValue('C' . $i, $title);
                $activeSheet->setCellValue('D' . $i, $item->product_barcode);
                $activeSheet->setCellValue('E' . $i, !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at) : '-');
                $activeSheet->setCellValue('F' . $i, !empty ($model->packing_date) ? Yii::$app->formatter->asDatetime($model->packing_date) : '-');
                $activeSheet->setCellValue('G' . $i, !empty ($model->date_left_warehouse) ? Yii::$app->formatter->asDatetime($model->date_left_warehouse) : '-');
                $activeSheet->setCellValue('H' . $i, $item->accepted_qty);
                $activeSheet->setCellValue('I' . $i, $item->product_name);
                $activeSheet->setCellValue('J' . $i, $row);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="outbound-orders-report-car-parts-billing.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }
	
	
	

    public function actionShippingListToExcel()
    {
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-' . date('d.m.Y'));

        $i = 1;
        $activeSheet->setCellValue('A' . $i, 'Номер заказа'); // +
        $activeSheet->setCellValue('B' . $i, 'Куда'); // +
        $activeSheet->setCellValue('C' . $i, 'Кол-во мест'); // +
        $activeSheet->setCellValue('D' . $i, 'Наш ТТН'); // +

        $search = new OutboundServiceReport();
        $activeDataProvider = $search->getSearch()->search(Yii::$app->request->queryParams);
        $activeDataProvider->pagination = false;
        $dps = $activeDataProvider->getModels();

        $storesArray = (new \common\modules\store\service\Service())->getStoreCityNameByClientWithPattern(Constants::getCarPartClientIDs());

        foreach ($dps as $model) {

            $store = Store::findOne($model->to_point_id);
            //if($store->region_id == 1) { // Все кроме Алматы
			if($store->region_id == 1) { // Все кроме Алматы
				if( $store->id != 555) {
					continue;
				}
            }

            $orderDp = TlDeliveryProposalOrders::find()->andWhere(['order_id'=>$model->id,'order_type'=>TlDeliveryProposalOrders::ORDER_TYPE_RPT])->one();
            $ourTTN = 'не найден';
            if ($orderDp) {
                $ourTTN =  $orderDp->tl_delivery_proposal_id;
            }

            $i++;
            $title = ArrayHelper::getValue($storesArray, $model->to_point_id);

            $activeSheet->setCellValue('A' . $i, $model->order_number);
            $activeSheet->setCellValue('B' . $i, $title);
            $activeSheet->setCellValue('C' . $i, $model->accepted_number_places_qty);
            $activeSheet->setCellValue('D' . $i,$ourTTN);

        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="shipping-list-('.date('Y-m-d').').xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }





    public function actionDeliveryOrder()
    {
        $search = new DeliveryOrderService();
        $activeDataProvider = $search->getSearch()->search(Yii::$app->request->queryParams);

        $clientsArray = Client::getActiveByIDs(Constants::getCarPartClientIDs());
        $storesArray = (new \common\modules\store\service\Service())->getStoreCityNameByClientWithPattern(Constants::getCarPartClientIDs());
        $routeDirectionArray = RouteDirections::getArrayData();
        return $this->render('delivery-order/index', [
            'activeDataProvider' => $activeDataProvider,
            'searchModel' => $search->getSearch(),
            'clientsArray' => $clientsArray,
            'storesArray' => $storesArray,
            'routeDirectionArray' => $routeDirectionArray,
        ]);
    }

}