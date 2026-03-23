<?php

use yii\db\Migration;

/**
 * Class m200528_105922_ecommerce_return_add_client_info
 */
class m200528_105922_ecommerce_return_add_client_info extends Migration
{
    public function up()
    {
        $this->addColumn('{{%ecommerce_return}}', 'client_ExternalShipmentId', $this->string(28)->defaultValue('')->comment("")->after('status'));
        $this->addColumn('{{%ecommerce_return}}', 'client_ExternalOrderId', $this->string(28)->defaultValue('')->comment("")->after('client_ExternalShipmentId'));
        $this->addColumn('{{%ecommerce_return}}', 'client_OrderSource', $this->string(28)->defaultValue('')->comment("")->after('client_ExternalOrderId'));
        $this->addColumn('{{%ecommerce_return}}', 'client_CargoReturnCode', $this->string(28)->defaultValue('')->comment("")->after('client_OrderSource'));
        $this->addColumn('{{%ecommerce_return}}', 'client_IsRefundable', $this->string(8)->defaultValue('')->comment("")->after('client_CargoReturnCode'));
        $this->addColumn('{{%ecommerce_return}}', 'client_RefundableMessage', $this->text()->defaultValue('')->comment("")->after('client_IsRefundable'));
        $this->addColumn('{{%ecommerce_return}}', 'return_reason', $this->string(8)->defaultValue('')->comment("")->after('client_RefundableMessage'));


        $this->addColumn('{{%ecommerce_return_items}}', 'client_SkuId', $this->string(16)->defaultValue('')->comment("")->after('status'));
        $this->addColumn('{{%ecommerce_return_items}}', 'client_ImageUrl', $this->text()->defaultValue('')->comment("")->after('client_SkuId'));
        $this->addColumn('{{%ecommerce_return_items}}', 'client_UnitPrice', $this->string(16)->defaultValue('')->comment("")->after('client_ImageUrl'));
        $this->addColumn('{{%ecommerce_return_items}}', 'client_UnitDiscount', $this->string(16)->defaultValue('')->comment("")->after('client_UnitPrice'));
        $this->addColumn('{{%ecommerce_return_items}}', 'client_SalesQuantity', $this->integer()->defaultValue(0)->comment("")->after('client_UnitDiscount'));
        $this->addColumn('{{%ecommerce_return_items}}', 'client_ReturnedQuantity', $this->integer()->defaultValue(0)->comment("")->after('client_SalesQuantity'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_return}}', 'client_ExternalShipmentId');
        $this->dropColumn('{{%ecommerce_return}}', 'client_ExternalOrderId');
        $this->dropColumn('{{%ecommerce_return}}', 'client_OrderSource');
        $this->dropColumn('{{%ecommerce_return}}', 'client_CargoReturnCode');
        $this->dropColumn('{{%ecommerce_return}}', 'client_IsRefundable');
        $this->dropColumn('{{%ecommerce_return}}', 'client_RefundableMessage');
        $this->dropColumn('{{%ecommerce_return}}', 'return_reason');

        $this->dropColumn('{{%ecommerce_return_items}}', 'client_SkuId');
        $this->dropColumn('{{%ecommerce_return_items}}', 'client_ImageUrl');
        $this->dropColumn('{{%ecommerce_return_items}}', 'client_UnitPrice');
        $this->dropColumn('{{%ecommerce_return_items}}', 'client_UnitDiscount');
        $this->dropColumn('{{%ecommerce_return_items}}', 'client_SalesQuantity');
        $this->dropColumn('{{%ecommerce_return_items}}', 'client_ReturnedQuantity');
        return false;
    }
}
