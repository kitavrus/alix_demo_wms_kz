<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 24.07.2019
 * Time: 19:59
 */

namespace common\ecommerce\constants;


use yii\helpers\ArrayHelper;

class ReturnReason
{
	/*
	 * @var status
	 * */
	const NOT_SET = 0;// Статус не определен
	const PRODUCT_QUALITY = 106;
	const MISSING_PRODUCT = 115;
	const DELAYED_SHIPPING = 105;
	const DAMAGED_CARGO = 118;
	const DEFORMATION_AFTER_USE = 197;
	const OTHER = 199;
	const STOCK_FAULTY = 196;
	///////////////////// NEW ///////////////////////////
	const FAULTY_PRODUCT_SENT = 101;
	const WRONG_PRODUCT_SENT = 102;
	const SIZE_PROBLEM = 103;
	const PRODUCT_WITH_DIFFERENT_LABEL_OR_PRICE = 198;
	const I_GIVE_UP = 104;
	const CANCEL_BEFORE_SHIPMENT = 117;
	const NO_RETURN_REASON_INDICATED = 111;
	const GET_COMPENSATION_FOR_CARGO_COMPANY = 113;
	const SALES_RETURN_WITHOUT_DELIVERING_TO_CUSTOMER = 107;
	const RETURNED_FOR_TESTING = 112;
	const BODY_IS_BIG = 119;
	const BODY_IS_SMALL = 120;
	const PRODUCT_WITH_VISUAL_AND_CONTENT_INFORMATION_DOES_NOT_MATCH = 121;
	const I_DID_NOT_LIKE_THE_PRODUCT = 122;
	const DELIVERY_WRONG_PRODUCT_TO_CUSTOMER = 123;
	const MISSING_CUSTOMER_RETURN = 124;
	const PAY_AT_STORE_UNAVAILABLE_STOCK = 230;
	const REJECTED_FROM_STORE_CUSTOMER_REQ_CANCEL_TO_PAS = 231;
	
	/**
	 * @return array Массив с статусами.
	 */
	public static function getAll()
	{
		return [
			self::OTHER => 'Другой',
            self::DAMAGED_CARGO => 'Поврежденный груз',
            self::SIZE_PROBLEM => 'Размер продукта',
            self::FAULTY_PRODUCT_SENT => 'Неисправный продукт',
            self::WRONG_PRODUCT_SENT => 'Неправильный продукт',
            self::PRODUCT_QUALITY => 'Качество продукции',
            self::MISSING_PRODUCT => 'Недостающий продук',
            self::PRODUCT_WITH_DIFFERENT_LABEL_OR_PRICE => 'Информация о наклейке разная',
            self::DELAYED_SHIPPING => 'Задержка доставки',
            self::DEFORMATION_AFTER_USE => 'Деформация после использования',
            self::STOCK_FAULTY => 'Запас неправильный',
		];
	}

	/**
	 * @param string $status Читабельный статус поста.
	 * @return string
	 */
	public static function getValue($status = null)
	{
		return ArrayHelper::getValue(self::getAll(), $status);
	}
}