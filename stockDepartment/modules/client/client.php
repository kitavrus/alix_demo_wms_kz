<?php

namespace app\modules\client;

use dektrium\user\Module as BaseUserModule;

class client extends BaseUserModule
{
    public $controllerNamespace = 'app\modules\client\controllers';

    /**
     * @var array An array of administrator's usernames.
     */
    public $admins = [];




    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    /**
     * Returns module components.
     * @return array
     */

}
