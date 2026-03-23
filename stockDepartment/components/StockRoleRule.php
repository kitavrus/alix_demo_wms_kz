<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 25.05.2016
 * Time: 10:25
 */

namespace stockDepartment\components;


use Yii;
use common\modules\employees\models\Employees;
use yii\helpers\VarDumper;

class StockRoleRule
{
    /*
     * @param array $menuItems Main menu items
     * */
    public static function showMainMenuByManagerType($menuItems)
    {
        if (!Yii::$app->user->isGuest) {
            if ($client = Employees::findOne(['user_id' => Yii::$app->user->id])) {
                switch ($client->manager_type) {
                    case Employees::TYPE_ACCOUNTANT: // Бухгалтер
                    case Employees::TYPE_MANAGER_WHS_MAIN: // Главный менеджер по складу
                    case Employees::TYPE_MANAGER_TRAFFIC_MAIN: // Главный менеджер по транспорту
                        break;
                    case Employees::TYPE_MAIN_STOCK_EMPLOYEE: // Общий аккаунт для склада
//                        unset($menuItems[1],$menuItems[2],$menuItems[3],$menuItems[4],$menuItems[5]);
                        unset($menuItems[2],$menuItems[3],$menuItems[4],$menuItems[5]);
                        break;
                    case Employees::TYPE_OPERATOR_DELLA: // Оператор по делле
                        unset($menuItems[0],$menuItems[1],$menuItems[2],$menuItems[3],$menuItems[4],$menuItems[5],$menuItems[6]);
//                        $menuItems = $menuItems[5]['items'];
//                        $menuItems = $menuItems[6]['items'];
                        break;
                    default:
                        break;
                }
            }
        }
//        VarDumper::dump($menuItems,10,true);
//        die;
        return $menuItems;
    }
}