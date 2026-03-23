<?php
namespace app\modules\ecommerce\controllers\defacto;

use common\ecommerce\defacto\stock\forms\StockAdjustmentForm;
use stockDepartment\components\Controller;
use Yii;

class StockAdjustmentController extends Controller
{
    public function actionIndex()
    {
        $form = new StockAdjustmentForm();
        $form->setScenario(StockAdjustmentForm::SCENARIO_ADD);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $form->change();
            Yii::$app->session->setFlash('success', 'Изменение внесено');
            return $this->redirect(['index']);
        }

        return $this->render('index',['model'=>$form]);
    }
}