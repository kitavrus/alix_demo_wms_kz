<?php

namespace app\modules\city;

use Yii;

class city extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\city\controllers';

    public function init()
    {
        parent::init();

        $this->registerTranslations();

    }

    public function registerTranslations()
    {
        Yii::$app->i18n->translations['modules/city/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@app/modules/city/messages',
            'fileMap' => [
                'modules/city/forms' => 'forms.php',
                'modules/city/titles' => 'titles.php',
                'modules/city/buttons' => 'buttons.php',
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('modules/city/' . $category, $message, $params, $language);
    }
}
