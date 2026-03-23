<?php
namespace stockDepartment\controllers;

use Yii;
ini_set('soap.wsdl_cache_enabled', 0);
/**
 * MieleAPI controller
 */
class MielController extends \yii\web\Controller // miele.com
{
    public $enableCsrfValidation = false;
    public $layout = false;
    public function init() {
        $this->enableCsrfValidation = false;
        $this->layout = "";
    }

    public function actions()
    {
        return [
            'hello' => [
                'class' => 'stockDepartment\controllers\MySoapServer',
            ],
        ];
    }
}