<?php

namespace app\modules\billing\controllers;

use common\modules\billing\models\TlDeliveryProposalBillingConditions;
use common\modules\store\models\Store;
use Yii;
use common\modules\billing\models\TlDeliveryProposalBilling;
use stockDepartment\modules\billing\models\TlDeliveryProposalBillingSearch;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\billing\components\BillingManager;
//use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use stockDepartment\components\Controller;
use yii\helpers\VarDumper;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\client\models\Client;
use common\components\DeliveryProposalManager;

/**
 * DefaultController implements the CRUD actions for TlDeliveryProposalBilling model.
 */
class DefaultController extends Controller
{

    /**
     * Lists all TlDeliveryProposalBilling models for all clients
     * @return mixed
     */
    public function actionIndex($tariffType = null)
    {
        $searchModel = new TlDeliveryProposalBillingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $storeArray = TLHelper::getStockPointArray();
        $clientArray = Client::getActiveWMSItems();
        //Для юр.лиц индивидуальные
        if ($tariffType == TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL) {
            $dataProvider->query->andFilterWhere(['tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL]);

            return $this->render('index', [
                'title' => Yii::t('titles', 'Manage company individual tariffs'),
                'searchModel' => $searchModel,
                'storeArray' => $storeArray,
                'clientArray' => $clientArray,
                'dataProvider' => $dataProvider,
                'tariffType' => $tariffType,
            ]);
            //Для физ.лиц индивидуальные
        } elseif ($tariffType == TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_INDIVIDUAL) {
            $dataProvider->query->andFilterWhere(['tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_INDIVIDUAL]);
            return $this->render('index', [
                'title' => Yii::t('titles', 'Manage person individual tariffs'),
                'searchModel' => $searchModel,
                'storeArray' => $storeArray,
                'clientArray' => $clientArray,
                'dataProvider' => $dataProvider,
                'tariffType' => $tariffType,
            ]);
            //Для юр.лиц по-умолчанию
        } elseif ($tariffType == TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT) {
            $dataProvider->query->andFilterWhere(['tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT]);
            return $this->render('index', [
                'title' => Yii::t('titles', 'Manage person default tariffs'),
                'searchModel' => $searchModel,
                'storeArray' => $storeArray,
                'clientArray' => $clientArray,
                'dataProvider' => $dataProvider,
                'tariffType' => $tariffType,
            ]);
            //Для физ.лиц по-умолчанию
        } elseif ($tariffType == TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_DEFAULT) {
            $dataProvider->query->andFilterWhere(['tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_DEFAULT]);
            return $this->render('index', [
                'title' => Yii::t('titles', 'Manage company default tariffs'),
                'searchModel' => $searchModel,
                'storeArray' => $storeArray,
                'clientArray' => $clientArray,
                'dataProvider' => $dataProvider,
                'tariffType' => $tariffType,
            ]);
        }

        return $this->render('index', [
            'title' => Yii::t('titles', 'Manage tariffs'),
            'searchModel' => $searchModel,
            'storeArray' => $storeArray,
            'clientArray' => $clientArray,
            'dataProvider' => $dataProvider,
            'tariffType' => $tariffType,
        ]);
    }


    /**
     * Recalculate prices of proposals, due to tariff changes
     * @param integer $id
     */
    public function actionRecalculateInvoicePrice()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = $offset = Yii::$app->request->post('id');
        $model = $this->findModel($id);

        $limit = 20;
        if(!($offset = Yii::$app->request->post('offset'))) {
            $offset = 0;
        }

        $count = TlDeliveryProposal::find()->andWhere('client_id=:client_id AND status_invoice!=:status_invoice AND route_from = :route_from AND route_to = :route_to',
                                                [':client_id'=>$model->client_id,
                                                 ':status_invoice'=>TlDeliveryProposal::INVOICE_PAID,
                                                 ':route_from'=>$model->route_from,
                                                 ':route_to'=>$model->route_to,
                                                ]
        )->count();

        $x = '';

        if($clientProposals = TlDeliveryProposal::find()->andWhere('client_id=:client_id AND status_invoice!=:status_invoice AND route_from = :route_from AND route_to = :route_to',[':client_id'=>$model->client_id, ':status_invoice'=>TlDeliveryProposal::INVOICE_PAID, ':route_from'=>$model->route_from,
            ':route_to'=>$model->route_to,])->limit($limit)->offset($offset)->all()) {
            $bm =  new BillingManager();
            foreach ($clientProposals as $proposal) {
                $x .= $proposal->id."\n";
                if($price_invoice_with_vat = $bm->getInvoicePriceForDP($proposal)) {
                    $proposal->price_invoice = $bm->getInvoicePriceForDP($proposal, false);
                    $proposal->price_invoice_with_vat = $price_invoice_with_vat;
                    $proposal->save(false);
                   //$proposal->recalculateExpensesOrder();

                    $dpManager = new DeliveryProposalManager(['id'=>$proposal->id]);
                    $dpManager->onRecalculateBilling();
                }
            }
        }

        return [
            'x'=>$x,
            'count'=>$count,
            'offset'=>$offset+20,
        ];
    }

    /**
     * Displays a single TlDeliveryProposalBilling model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TlDeliveryProposalBilling model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($tariffType)
    {
        $model = new TlDeliveryProposalBilling();
        $model->tariff_type = $tariffType;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TlDeliveryProposalBilling model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Copy an existing TlDeliveryProposalBilling model with related default conditions.
     * If copy is successful, the browser will be redirected to the 'update' page.
     * @param integer $id
     * @return mixed
     */
    public function actionCopy($id)
    {
        $model = $this->findModel($id);

        if ($model) {
            $attributes = $model->getAttributes(null,
                ['created_user_id', 'updated_user_id', 'created_at', 'updated_at']);

            if (!empty($attributes)) {
                $clone = new TlDeliveryProposalBilling();
                $clone->setAttributes($attributes);
                if ($clone->save(false)) {
                    $conditions = $model->conditions;
                    if (!empty($conditions)) {
                        foreach ($conditions as $condition) {
                            $condition_attributes = $condition->getAttributes(null,
                                ['created_user_id', 'updated_user_id', 'created_at', 'updated_at']);
                            if (!empty($condition_attributes)) {
                                $clone_condition = new TlDeliveryProposalBillingConditions();
                                $clone_condition->setAttributes($condition_attributes);
                                $clone_condition->tl_delivery_proposal_billing_id = $clone->id;
                                $clone_condition->save();
                            }
                        }
                    }
                    return $this->redirect(['update', 'id' => $clone->id]);
                }

            }
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /*
  * Import to excel
  *
  **/
    public function actionExportToExcel()
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
        $activeSheet->setCellValue('B' . $i, 'Город откуда'); // +
        $activeSheet->setCellValue('C' . $i, 'Город куда'); // +
        $activeSheet->setCellValue('D' . $i, 'Откуда'); // +
        $activeSheet->setCellValue('E' . $i, 'Куда'); // +
        $activeSheet->setCellValue('F' . $i, 'Цена'); // +
        $activeSheet->setCellValue('G' . $i, 'Тип подсчета'); // +
        $activeSheet->setCellValue('H' . $i, 'Сроки доставки'); // +

        $searchModel = new TlDeliveryProposalBillingSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['tariff_type' => TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL]);
        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();
        $if = Yii::$app->request->get('if');

        foreach ($dps as $model) {
            $i++;

            $activeSheet->setCellValue('A' . $i, ArrayHelper::getValue($model->client,'title'));
            $activeSheet->setCellValue('B' . $i, ArrayHelper::getValue($model->city,'name'));
            $activeSheet->setCellValue('C' . $i, ArrayHelper::getValue($model->cityTo,'name'));
            $activeSheet->setCellValue('D' . $i, Store::getPointTitle($model->route_from));
            $activeSheet->setCellValue('E' . $i,  Store::getPointTitle($model->route_to));
            $activeSheet->setCellValue('F' . $i, $model->price_invoice_with_vat);
            $activeSheet->setCellValue('G' . $i, $model->getRuleType());
            $activeSheet->setCellValue('H' . $i, $model->delivery_term_from.' до '. $model->delivery_term_to);

            if($if) {
                if ($conditions = $model->conditions) {
                    foreach ($conditions as $itemCondition) {
                        $i++;
                        $activeSheet->setCellValue('A' . $i, $itemCondition->formula_tariff);
                        $activeSheet->setCellValue('B' . $i, $itemCondition->price_invoice);
                        $activeSheet->setCellValue('C' . $i, $itemCondition->price_invoice_with_vat);
                    }
                }
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="delivery-proposal-billing-report-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    /**
     * Deletes an existing TlDeliveryProposalBilling model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionDelete($id)
//    {
////        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the TlDeliveryProposalBilling model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TlDeliveryProposalBilling the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TlDeliveryProposalBilling::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
