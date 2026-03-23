<?php

namespace app\modules\transportLogistics;
use Yii;

class transportLogistics extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\transportLogistics\controllers';

    public function init()
    {
        parent::init();

       // $this->registerTranslations();

    }

//    public function registerTranslations()
//    {
//        Yii::$app->i18n->translations['modules/transportLogistics/*'] = [
//            'class' => 'yii\i18n\PhpMessageSource',
//            'sourceLanguage' => 'en-US',
//            'basePath' => '@app/modules/transportLogistics/messages',
//            'fileMap' => [
//                'modules/transportLogistics/forms' => 'forms.php',
//                'modules/transportLogistics/titles' => 'titles.php',
//                'modules/transportLogistics/buttons' => 'buttons.php',
//            ],
//        ];
//    }
//
//    public static function t($category, $message, $params = [], $language = null)
//    {
//        return Yii::t('modules/transportLogistics/' . $category, $message, $params, $language);
//    }
}
