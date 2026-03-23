<?php
namespace app\modules\ecommerce\controllers\defacto;

use common\ecommerce\defacto\checkBox\forms\CheckBoxForm;
use common\ecommerce\defacto\checkBox\repository\EcommerceCheckBoxSearch;
use common\ecommerce\defacto\checkBox\repository\CheckBoxRepository;
use common\ecommerce\defacto\checkBox\service\CheckBoxService;
use common\ecommerce\defacto\employee\repository\EmployeeRepository;
use common\ecommerce\entities\EcommerceCheckBox;
use common\ecommerce\entities\EcommerceCheckBoxStock;
use stockDepartment\components\Controller;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\helpers\VarDumper;
use yii\web\Response;

class CheckBoxController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new EcommerceCheckBoxSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        $service = new \common\ecommerce\defacto\checkBox\service\CheckBoxService();
        $allInventoryKeyList = $service->getAllInventoryKeyList();


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'allInventoryKeyList' => $allInventoryKeyList,
        ]);
    }

    public function actionView($id)
    {
        $repository = new CheckBoxRepository();
        $checkBox = $repository->getById($id);
        $service = new CheckBoxService();

        $employee = new EmployeeRepository();
        $employeeInfo = $employee->getById($checkBox->employee_id);

        $dto = new \stdClass();
        $dto->employeeBarcode = $employeeInfo->barcode;
        $dto->inventoryId = $checkBox->inventory_id;
        $dto->boxBarcode = $checkBox->box_barcode;
        $dto->productBarcode = '';
        $dto->placeAddress = $checkBox->place_address;

        $productsInBox = $this->renderPartial('_products_in_box',['productListInBox'=>$service->showProductsInBox($dto)]);

        return $this->render('view', [
            'checkBox'=>$checkBox,
            'productsInBox'=>$productsInBox,
        ]);
    }

    public function actionScanning()
    {
        $checkBoxForm = new CheckBoxForm();
        $checkBoxForm->inventoryId = Yii::$app->request->get('inventoryId');
//        $checkBoxForm->inventoryKey = date('d-m-Y');
//        $checkBoxForm->title = date('d-m-Y');
//        VarDumper::dump(Yii::$app->request->get('inventoryId'),10,true);
//        die;
        return $this->render('scanning', [
            'checkBoxForm' => $checkBoxForm,
        ]);
    }

    public function actionEmployeeBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $checkBoxForm = new CheckBoxForm();
        $checkBoxForm->setScenario(CheckBoxForm::SCENARIO_EMPLOYEE_BARCODE);

        $errors = [];
        $result = [];
        if ($checkBoxForm->load(Yii::$app->request->post()) && $checkBoxForm->validate()) {

            $dto = $checkBoxForm->getDTO();
            $service = new CheckBoxService();
            $result = $service->employeeBarcode($dto);

        } else {
            $errors = ActiveForm::validate($checkBoxForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'result' => $result,
        ];
    }

    public function actionInventoryId()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $checkBoxForm = new CheckBoxForm();
        $checkBoxForm->setScenario(CheckBoxForm::SCENARIO_INVENTORY_ID);

        $errors = [];
        $result = [];
        if ($checkBoxForm->load(Yii::$app->request->post()) && $checkBoxForm->validate()) {

            $dto = $checkBoxForm->getDTO();
            $service = new CheckBoxService();
            $result = $service->inventoryId($dto);

        } else {
            $errors = ActiveForm::validate($checkBoxForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'result' => $result,
        ];
    }

    public function actionPlaceBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $checkBoxForm = new CheckBoxForm();
        $checkBoxForm->setScenario(CheckBoxForm::SCENARIO_PLACE_BARCODE);

        $errors = [];
        $result = [];
        if ($checkBoxForm->load(Yii::$app->request->post()) && $checkBoxForm->validate()) {

            $dto = $checkBoxForm->getDTO();
            $service = new CheckBoxService();
            $result = $service->placeBarcode($dto);

        } else {
            $errors = ActiveForm::validate($checkBoxForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'result' => $result,
        ];
    }

    public function actionBoxBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $checkBoxForm = new CheckBoxForm();
        $checkBoxForm->setScenario(CheckBoxForm::SCENARIO_BOX_BARCODE);

        $errors = [];
        $result = [];
        if ($checkBoxForm->load(Yii::$app->request->post()) && $checkBoxForm->validate()) {

            $dto = $checkBoxForm->getDTO();
            $service = new CheckBoxService();
            $result = $service->boxBarcode($dto);
            $productListInBox = $service->showProductsInBox($dto);
            $result->showProductsInBoxHTML = $this->renderPartial('_products_in_box',['productListInBox'=>$productListInBox]);

        } else {
            $errors = ActiveForm::validate($checkBoxForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'result' => $result,
        ];
    }

    public function actionProductBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $checkBoxForm = new CheckBoxForm();
        $checkBoxForm->setScenario(CheckBoxForm::SCENARIO_PRODUCT_BARCODE);

        $errors = [];
        $result = '';
        if ($checkBoxForm->load(Yii::$app->request->post()) && $checkBoxForm->validate()) {

            $dto = $checkBoxForm->getDTO();
            $service = new CheckBoxService();
            $result =  $service->productBarcode($dto);
            $productListInBox = $service->showProductsInBox($dto);
            $result->showProductsInBoxHTML = $this->renderPartial('_products_in_box',['productListInBox'=>$productListInBox]);

        } else {
            $errors = ActiveForm::validate($checkBoxForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'result' => $result,
        ];
    }


    public function actionShowProductsInBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $checkBoxForm = new CheckBoxForm();
        $checkBoxForm->setScenario(CheckBoxForm::SCENARIO_BOX_BARCODE);

        $errors = [];
        $result = '';
        if ($checkBoxForm->load(Yii::$app->request->post()) && $checkBoxForm->validate()) {

            $dto = $checkBoxForm->getDTO();
            $service = new CheckBoxService();
            $productListInBox = $service->showProductsInBox($dto);
            $result = $this->renderPartial('_products_in_box',['productListInBox'=>$productListInBox]);
        } else {
            $errors = ActiveForm::validate($checkBoxForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'result' => $result,
        ];
    }

    public function actionShowPackedButNotScannedToList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $checkBoxForm = new CheckBoxForm();
        $checkBoxForm->setScenario(CheckBoxForm::SCENARIO_BOX_BARCODE);

        $errors = [];
        $result = '';
        if ($checkBoxForm->load(Yii::$app->request->post()) && $checkBoxForm->validate()) {

            $dto = $checkBoxForm->getDTO();
            $service = new CheckBoxService();
            $productListInBox = $service->showProductsInBox($dto);
            $result = $this->renderPartial('_products_in_box',['productListInBox'=>$productListInBox]);
        } else {
            $errors = ActiveForm::validate($checkBoxForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'result' => $result,
        ];
    }

    public function actionEmptyBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $checkBoxForm = new CheckBoxForm();
        $checkBoxForm->setScenario(CheckBoxForm::SCENARIO_BOX_BARCODE);

        $errors = [];
        $result = '';
        if ($checkBoxForm->load(Yii::$app->request->post()) && $checkBoxForm->validate()) {

            $dto = $checkBoxForm->getDTO();
            $service = new CheckBoxService();
            $result = $service->emptyBox($dto);
        } else {
            $errors = ActiveForm::validate($checkBoxForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'result' => $result,
        ];
    }

    public function actionExportToExcel($showDiff = null) {

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
        $activeSheet->setCellValue('A' . $i, 'inventory_key')->getColumnDimension('A')->setAutoSize(true); // +
        $activeSheet->setCellValue('B' . $i, 'box_barcode')->getColumnDimension('B')->setAutoSize(true); // +
        $activeSheet->setCellValue('C' . $i, 'place_address')->getColumnDimension('C')->setAutoSize(true); // +
        $activeSheet->setCellValue('D' . $i, 'expected_qty')->getColumnDimension('D')->setAutoSize(true); // +
        $activeSheet->setCellValue('E' . $i, 'scanned_qty')->getColumnDimension('E')->setAutoSize(true); // +


        $searchModel = new EcommerceCheckBoxSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

        $service = new \common\ecommerce\defacto\checkBox\service\CheckBoxService();
        $allInventoryKeyList = $service->getAllInventoryKeyList();

        foreach ($dps as $model) {
            $i++;

            if(!is_null($showDiff)) {
                if($model['expected_qty'] == $model['scanned_qty']) {
                    continue;
                }
            }

            $activeSheet->setCellValue('A' . $i, \yii\helpers\ArrayHelper::getValue($allInventoryKeyList,$model['inventory_id']));
            $activeSheet->setCellValue('B' . $i, $model['box_barcode']);
            $activeSheet->setCellValue('C' . $i, $model['place_address']);
            $activeSheet->setCellValue('D' . $i, $model['expected_qty']);
            $activeSheet->setCellValue('E' . $i, $model['scanned_qty']);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="inventoryBoxExportToExcel-' . date('d-m-Y_H-i-s') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    public function actionExportToExcelWithProducts($showDiff = null) {

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
        $activeSheet->setCellValue('A' . $i, 'inventory_key')->getColumnDimension('A')->setAutoSize(true); // +
        $activeSheet->setCellValue('B' . $i, 'box_barcode')->getColumnDimension('B')->setAutoSize(true); // +
        $activeSheet->setCellValue('C' . $i, 'place_address')->getColumnDimension('C')->setAutoSize(true); // +
        $activeSheet->setCellValue('D' . $i, 'product_barcode')->getColumnDimension('D')->setAutoSize(true); // +
        $activeSheet->setCellValue('E' . $i, 'status')->getColumnDimension('E')->setAutoSize(true); // +
        $activeSheet->setCellValue('F' . $i, 'skuId')->getColumnDimension('F')->setAutoSize(true); // +


        $searchModel = new EcommerceCheckBoxSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

        $service = new \common\ecommerce\defacto\checkBox\service\CheckBoxService();
        $allInventoryKeyList = $service->getAllInventoryKeyList();

        foreach ($dps as $checkBox) {

            if(!is_null($showDiff)) {
                if($checkBox['expected_qty'] == $checkBox['scanned_qty']) {
                    continue;
                }
            }

            $checkBoxList = EcommerceCheckBoxStock::find()->andWhere(['check_box_id'=>$checkBox['id']])->orderBy('status')->all();
            foreach ($checkBoxList as $productRow) {

                if(!is_null($showDiff)) {
                    if($productRow['status'] == \common\ecommerce\constants\CheckBoxStatus::END_SCANNED) {
                        continue;
                    }
                }

                $i++;
                $activeSheet->setCellValue('A' . $i, \yii\helpers\ArrayHelper::getValue($allInventoryKeyList,$productRow['inventory_id']));
                $activeSheet->setCellValue('B' . $i, $productRow['box_barcode']);
                $activeSheet->setCellValue('C' . $i, $productRow['place_address']);
                $activeSheet->setCellValue('D' . $i, $productRow['product_barcode']);
                $activeSheet->setCellValue('E' . $i, \common\ecommerce\constants\CheckBoxStatus::getValue($productRow['status']));
                $activeSheet->setCellValue('F' . $i, $productRow['stock_client_product_sku']);
            }
            $i++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="inventoryExportToExcelWithProducts-' . date('d-m-Y_H-i-s') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

}