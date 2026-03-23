<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.02.2018
 * Time: 13:54
 */

namespace common\queueJobs;


use yii\base\BaseObject;

class TestJob extends BaseObject implements \yii\queue\JobInterface
{
    public $url;
    public $file;

    public function execute($queue)
    {
        file_put_contents('xxxxxxx.php', $this->url);
    }
}