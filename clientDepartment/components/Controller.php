<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 31.07.14
 * Time: 7:48
 */
namespace clientDepartment\components;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use clientDepartment\modules\client\components\ClientManager;
use common\modules\client\models\ClientEmployees;

/**
 * Основной контроллер frontend-приложения.
 * От данного контроллера унаследуются все остальные контроллеры frontend-приложения.
 */
class Controller extends \yii\web\Controller
{
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

    /*
        * Get view type
        * @param string $view Action name
        * @return string view name
        * */
    protected function getViewByType($view)
    {
        if(!Yii::$app->user->isGuest) {

//            if($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {

                if ($client = ClientManager::getClientEmployeeByAuthUser()) {
                    switch ($client->manager_type) {
                        case ClientEmployees::TYPE_BASE_ACCOUNT:
                        case ClientEmployees::TYPE_LOGIST:
                            $view .= '';
                            break;

                        case ClientEmployees::TYPE_DIRECTOR:
                        case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        case ClientEmployees::TYPE_MANAGER:
                        case ClientEmployees::TYPE_MANAGER_INTERN:
                            $view = 'manager/'.$view;
                            break;
                        case ClientEmployees::TYPE_OBSERVER:
                        case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
//                        case ClientEmployees::TYPE_REGIONAL_OBSERVER:
                        case ClientEmployees::TYPE_REGIONAL_OBSERVER_RUSSIA:
                        case ClientEmployees::TYPE_REGIONAL_OBSERVER_BELARUS:
                            $view = 'observer/'.$view;
                            break;

                        default:
                            $view = 'empty-default';
                            break;
                    }
//                }
            } else {
                $view = 'empty-default';
            }
        } else {
            $view = 'empty-default';
        }
        return $view;
    }
}