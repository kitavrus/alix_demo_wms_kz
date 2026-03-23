<?php

use yii\db\Migration;

/**
 * Class m191029_133711_ecommerce_api_log_change_field_data_size
 */
class m191029_133711_ecommerce_api_log_change_field_data_size extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `ecommerce_api_inbound_log` CHANGE `request_data` `request_data` longtext COLLATE 'utf8_general_ci' NULL COMMENT 'Request data' AFTER `response_is_success`, CHANGE `response_data` `response_data` longtext COLLATE 'utf8_general_ci' NULL COMMENT 'Response data' AFTER `request_data`;");
        $this->execute("ALTER TABLE `ecommerce_api_other_log` CHANGE `request_data` `request_data` longtext COLLATE 'utf8_general_ci' NULL COMMENT 'Request data' AFTER `response_is_success`, CHANGE `response_data` `response_data` longtext COLLATE 'utf8_general_ci' NULL COMMENT 'Response data' AFTER `request_data`;");
        $this->execute("ALTER TABLE `ecommerce_api_outbound_log` CHANGE `request_data` `request_data` longtext COLLATE 'utf8_general_ci' NULL COMMENT 'Request data' AFTER `response_is_success`, CHANGE `response_data` `response_data` longtext COLLATE 'utf8_general_ci' NULL COMMENT 'Response data' AFTER `request_data`;");
    }

    public function down()
    {
        return false;
    }
}