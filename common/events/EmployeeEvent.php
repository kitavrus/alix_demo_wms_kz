<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\events;

use yii\base\Event;

/**
*
 */
class EmployeeEvent extends Event
{
    /**
     * @var boolean $insert If true insert else update
     */
    public $insert;
}
