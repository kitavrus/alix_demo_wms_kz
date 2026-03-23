<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 31.07.14
 * Time: 7:48
 */
namespace stockDepartment\components;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;

/**
 * Основной контроллер stockDepartment-приложения.
 * От данного контроллера унаследуются все остальные контроллеры stockDepartment-приложения.
 */
class Controller extends \yii\web\Controller
{
    private $_printType;

    /*
     *
     * Base behaviors
     * */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
    public function init() {
        if(!\Yii::$app->user->isGuest) {
            if(!in_array(\Yii::$app->user->id,[3,6,183,206,216,217,220,341]) && !in_array(\Yii::$app->user->identity->username,['Mjalilov','Atoxanov'])) {
                // Aualiev
                // Snurgalieva
                // E-Omurzakov
                // iPotema
                // Bermet
                // TYPE_MAIN_STOCK_EMPLOYEE StockMan 1234567899
                // della_operator
                // Абзал
                // убрал Аскара
                \Yii::$app->getUser()->logout();
            }
        }
        return parent::init();
    }

    /**
     * Soft delete.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model=$this->findModel($id);
            if($model){
                $model->deleted = 1;
                $model->save(false);
            }
        return $this->redirect(['index']);
    }

    /**
     *Printing format: html, pdf
     */
    public function getPrintType()
    {
        $this->_printType = \Yii::$app->params['printType'];
        return $this->_printType;
    }

}