<?php

namespace app\modules\wms\controllers\carParts\main;

use common\clientObject\constants\Constants;
use common\clientObject\main\inbound\service\InboundServiceReport;
use common\clientObject\deliveryProposal\service\DeliveryOrderService;
use common\clientObject\main\outbound\service\ABCServiceReport;
use common\clientObject\main\outbound\service\ABCZombieServiceReport;
use common\clientObject\main\outbound\service\OutboundServiceReport;
use common\modules\city\models\RouteDirections;
use common\modules\client\models\Client;
//use common\modules\outbound\models\OutboundOrderItem;
use common\modules\stock\models\Stock;
use stockDepartment\components\Controller;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class AbcController extends Controller
{
    public function actionIndex()
    {
        $search = new ABCServiceReport();
        $activeDataProvider = $search->getSearch()->search(Yii::$app->request->queryParams);

        $clientsArray = Client::getActiveByIDs(Constants::getCarPartClientIDs());

        return $this->render('index', [
            'activeDataProvider' => $activeDataProvider,
            'searchModel' => $search->getSearch(),
            'clientsArray' => $clientsArray,
        ]);
    }

    public function actionToExcel()
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
        $activeSheet->setCellValue('B' . $i, 'ШК товара'); // +
        $activeSheet->setCellValue('C' . $i, 'Адрес'); // +
        $activeSheet->setCellValue('D' . $i, 'Короб'); // +
        $activeSheet->setCellValue('E' . $i, 'Кол-во'); // +
//        $activeSheet->setCellValue('F' . $i, 'Дата упаковки'); // +
//        $activeSheet->setCellValue('G' . $i, 'Дата отгрузки');

        $search = new ABCServiceReport();
        $activeDataProvider = $search->getSearch()->search(Yii::$app->request->queryParams);
        $activeDataProvider->pagination = false;
        $dps = $activeDataProvider->getModels();

        $clientsArray = Client::getActiveByIDs(Constants::getCarPartClientIDs());
        foreach ($dps as $model) {
            $i++;
            $clientTitle = ArrayHelper::getValue($clientsArray,$model['client_id']);

            $productInStockWithAddress = Stock::find()->select('primary_address, secondary_address, count(id) as productQty, status_availability')
                ->andWhere(['product_barcode'=> $model['product_barcode'],'client_id'=> $model['client_id'],'status_availability'=>Stock::STATUS_AVAILABILITY_YES])
                ->groupBy('primary_address, secondary_address, product_barcode, status_availability')
                ->asArray()
                ->all();

            if (empty($productInStockWithAddress)) { continue; }

            $activeSheet->setCellValue('A' . $i, $clientTitle);
            $activeSheet->setCellValue('B' . $i, $model['product_barcode']);
            $activeSheet->setCellValue('E' . $i, $model['productQty']);

            foreach($productInStockWithAddress as $product) {
                $i++;
                $activeSheet->setCellValue('A' . $i,'');
                $activeSheet->setCellValue('B' . $i, '');
                $activeSheet->setCellValue('C' . $i, $product['secondary_address']);
                $activeSheet->setCellValue('D' . $i, $product['primary_address']);
                $activeSheet->setCellValue('E' . $i, $product['productQty']);
            }
            $i++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="stock-abc-report.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    public function actionIndexZombie()
    {
        $search = new ABCZombieServiceReport();
        $activeDataProvider = $search->getSearch()->search(Yii::$app->request->queryParams);

        $clientsArray = Client::getActiveByIDs(Constants::getCarPartClientIDs());

        return $this->render('index-zombie', [
            'activeDataProvider' => $activeDataProvider,
            'searchModel' => $search->getSearch(),
            'clientsArray' => $clientsArray,
        ]);
    }

    public function actionToExcelBoxZombie()
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
        $activeSheet->setCellValue('B' . $i, 'Адрес'); // +
        $activeSheet->setCellValue('C' . $i, 'Короб'); // +

        $search = new ABCZombieServiceReport();
        $activeDataProvider = $search->getSearch()->searchZombieBox(Yii::$app->request->queryParams);
        $activeDataProvider->pagination = false;
        $dps = $activeDataProvider->getModels();

        $clientsArray = Client::getActiveByIDs(Constants::getCarPartClientIDs());
        foreach ($dps as $model) {
            $i++;
            $clientTitle = ArrayHelper::getValue($clientsArray,$model['client_id']);

            $activeSheet->setCellValue('A' . $i, $clientTitle);
            $activeSheet->setCellValue('B' . $i, $model['secondary_address']);
            $activeSheet->setCellValue('C' . $i, $model['primary_address']);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="stock-abc-report.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    public function actionToExcelZombie()
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
        $activeSheet->setCellValue('B' . $i, 'ШК товара'); // +
        $activeSheet->setCellValue('C' . $i, 'Адрес'); // +
        $activeSheet->setCellValue('D' . $i, 'Короб'); // +
        $activeSheet->setCellValue('E' . $i, 'Кол-во'); // +

        $search = new ABCZombieServiceReport();
        $activeDataProvider = $search->getSearch()->search(Yii::$app->request->queryParams);
        $activeDataProvider->pagination = false;
        $dps = $activeDataProvider->getModels();

        $clientsArray = Client::getActiveByIDs(Constants::getCarPartClientIDs());
        foreach ($dps as $model) {
            $i++;
            $clientTitle = ArrayHelper::getValue($clientsArray,$model['client_id']);

            $productInStockWithAddress = Stock::find()->select('primary_address, secondary_address, count(id) as productQty, status_availability')
                ->andWhere(['product_barcode'=> $model['product_barcode'],'client_id'=> $model['client_id'],'status_availability'=>Stock::STATUS_AVAILABILITY_YES])
                ->groupBy('primary_address, secondary_address, product_barcode, status_availability')
                ->orderBy('secondary_address')
                ->asArray()
                ->all();

            if (empty($productInStockWithAddress)) { continue; }

            $activeSheet->setCellValue('A' . $i, $clientTitle);
            $activeSheet->setCellValue('B' . $i, $model['product_barcode']);
            $activeSheet->setCellValue('E' . $i, $model['productQty']);

            foreach($productInStockWithAddress as $product) {
                $i++;
                $activeSheet->setCellValue('A' . $i,'');
                $activeSheet->setCellValue('B' . $i, '');
                $activeSheet->setCellValue('C' . $i, $product['secondary_address']);
                $activeSheet->setCellValue('D' . $i, $product['primary_address']);
                $activeSheet->setCellValue('E' . $i, $product['productQty']);
            }
            $i++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="stock-abc-report.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }
}