<?php

namespace app\modules\stock\controllers;

use common\modules\stock\models\RackAddressSearch;
use stockDepartment\modules\stock\models\PrintAddressForm;
use Yii;
use common\modules\stock\models\Stock;
use stockDepartment\components\Controller;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\modules\stock\models\RackAddress;
use stockDepartment\modules\stock\models\GenerateAddressForm;

/**
 * StockController implements the CRUD actions for Stock model.
 */
class AddressController extends Controller
{
    /**
     * Lists all Stock models.
     * @return mixed
     */
    public function actionGenerateAddress()
    {
        $model = new GenerateAddressForm();
        $searchModel = new RackAddressSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $successCount = 0;

            // STAGE
            for ($i1 = $model->stageMin; $i1 <= $model->stageMax; $i1++) {
                // ROW
                for ($i2 = $model->rowMin; $i2 <= $model->rowMax; $i2++) {
                    // RACK
                    for ($i3 = $model->rackMin; $i3 <= $model->rackMax; $i3++) {
                        // LEVEL
                        for ($i4 = $model->levelMin; $i4 <= $model->levelMax; $i4++) {
                            $stageValue = ($i1 == 0) ? null : $i1;
                            if (GenerateAddressForm::createAddress($stageValue, $i2, $i3, $i4)) {
                                $successCount++;
                            }
                        }
                    }
                }
            }

            $messageSuccess = Yii::t('app', 'Адреса успешно созданы', [
                'success' => $successCount,
            ]);

            $messageError = Yii::t('app', 'Не удалось создать адреса');

            if ($successCount > 0) {
                Yii::$app->session->addFlash('success', $messageSuccess);
            } else {
                Yii::$app->session->addFlash('error', $messageError);
            }

            return $this->refresh();
        }

        return $this->render('generate-address', [
            'generateModel' => $model,
            'printModel' => new PrintAddressForm(),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPrintAddress()
    {
        $model = new PrintAddressForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $addresses = [];
            $printSize = $model->printSize;

            for ($stage = $model->stageMin; $stage <= $model->stageMax; $stage++) {
                for ($row = $model->rowMin; $row <= $model->rowMax; $row++) {
                    for ($rack = $model->rackMin; $rack <= $model->rackMax; $rack++) {
                        for ($level = $model->levelMin; $level <= $model->levelMax; $level++) {
                            $parts = [];
                            if ($stage != 0) {
                                $parts[] = $stage;
                            }
                            $parts[] = $row;
                            $parts[] = sprintf('%02d', $rack);
                            if ($level != 0) {
                                $parts[] = $level;
                            }
                            $addresses[] = implode('-', $parts);
                        }
                    }
                }
            }

			echo "address from generate: ";
			VarDumper::dump($addresses,10,true);
            $addresses = RackAddress::find()
                ->select('address')
                ->andWhere(['address' => $addresses])
                ->asArray()
                ->column();
            echo "add res from DB: ";
            VarDumper::dump($addresses,10,true);

            if ($printSize === 'A4') {
                $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8');
            } else {
                // Для этикетки
                $pdf = new \TCPDF('L', 'mm', [$model->labelWidth, $model->labelHeight], true, 'UTF-8');
            }
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('nmdx.com');
            $pdf->SetTitle('Product labels');
            $pdf->SetSubject('Product labels');
            $pdf->SetKeywords('nmdx.com, product, label');
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(5, 5, 5, true);
            $pdf->SetAutoPageBreak(false, 0);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

            if ($printSize === 'A4') {
                $pdf->AddPage('L', 'A4', true);
            } else {
                $pdf->AddPage('L', [$model->labelWidth, $model->labelHeight], true);
            }

            $row = 0;
            $stepX = 100;
            $stepSumX = 5;
            $stepY = 50;
            $stepSumY = 5;

            foreach ($addresses as $address) {
                $style = array(
                    'align' => 'C',
                    'stretch' => false,
                    'fitwidth' => false,
                    'cellfitalign' => '',
                    'border' => false,
                    'padding' => 0,
                    'hpadding' => 0,
                    'vpadding' => 0.5,
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => false,
                    'text' => false,
                    'font' => 'arial',
                    'fontsize' => 40,
                    'stretchtext' => 1
                );

                $pdf->write1DBarcode($address, 'C128', $stepSumX, $stepSumY, 85, 10, 0.8, $style);
                $pdf->SetFont('Arial', 'B', 43);
                $pdf->writeHTMLCell(0, 0, $stepSumX - 3, $stepSumY + 10, "<h1>" . $address . "</h1>", 0, 0, false, false, 'L');

                $stepSumX += $stepX;
                $row++;

                if ($printSize === 'A4') {
                    if ($row % 3 == 0) {
                        $stepSumX = 5;
                        $stepSumY += $stepY;
                    }
                    if ($row % 3 && $row != 0) {
                        $stepSumX -= 5;
                    }
                    if ($row % 12 == 0) {
                        $stepSumX = $stepSumY = 5;
                        $row = 0;
                        $pdf->AddPage('L', 'A4', true);
                    }
                } else {
                    // Для этикетки — каждая на новой странице
                    $pdf->AddPage('L', [$model->labelWidth, $model->labelHeight], true);
                }
            }

            $pdf->lastPage();
            $pdf->Output('address-barcodes.pdf', 'D');
            Yii::$app->end();
        }

        return $this->render('generate-address', [
            'printModel' => $model,
        ]);
    }

    /**
     * Lists all Stock models.
     * @return mixed
     */
    public function actionStockAddress()
    {
        $address = Yii::$app->request->get('address');
        if ($model = RackAddress::findOne(['address'=>$address])){
            return $this->render('print/address-barcode-pdf_a4', ['addresses' => [$model->address]]);
        } else {
            Yii::$app->session->setFlash('danger', 'Вы не указали номер полки');
        }

        return $this->render('stock-address');
    }
}