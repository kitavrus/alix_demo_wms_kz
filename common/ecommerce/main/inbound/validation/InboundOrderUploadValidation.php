<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 12:23
 */

namespace common\ecommerce\main\inbound\validation;


class InboundOrderUploadValidation extends \common\ecommerce\main\inbound\validation\Validation
{
    /**
     * Validation constructor.
     * @param $config array
     * @param $params array
     */
    public function __construct($config = [],$params = [])
    {
        parent::__construct($config,$params);
    }
}