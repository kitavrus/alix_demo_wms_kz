<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 24.07.2019
 * Time: 19:59
 */

namespace common\ecommerce\constants;


use yii\helpers\ArrayHelper;

class OutboundCancelStatus
{
	/*
	 * @var status
	 * */
	const NOT_SET = 0;// Статус не определен

//    const UNABLE_TO_FULFIL = 1; //- Нет на стоке
//    const CUSTOMER_REQUESTS_CANCELLATION = 2; //  Отказ клиента
//	const UN_COLLECTED = 3; //  Есть в стоке но не нашли
//    const FRAUD = 4; //  Это ошибочный заказ или обман
//    const ENTER_CANCELLATION_REASON_MANUALLY = 5; // Указываем руками причину отмены
//	const DAMAGE_PRODUCT = 'DamageProduct'; // Указываем руками причину отмены
//	const PARTIAL_CANCEL = 'PartialCancel'; // частичная отмена если ожидали 4 а отгрузили 3 товара
//	const ON_TRANSFER_B2C_TO_B2B = 'ON_TRANSFER_B2C_TO_B2B'; // товары которые в трансфере
	///// NEW //////////////////

	const UNABLE_TO_FULFIL = 201;
	const CUSTOMER_REQUESTS_CANCELLATION = 202;
	const UN_COLLECTED = 203;
	const FRAUD = 204;
	const SYSTEM_API_ERROR = 205;
	const TEST_ORDER = 206;
	const ENTER_CANCELLATION_REASON_MANUALLY = 299;
	const BANK_SPENDING_OBJECTION = 207;
	const TOOL_DETECTION = 208;
	const CUSTOMER_OBJECTION = 209;
	const REFUSE_TO_BUY = 210;
	const DONT_WANT_SHARE_INFORMATION = 211;
	const ADDRESS_CHANGE_FAILED = 212;
	const ADD_NEW_PRODUCT_TO_ORDER = 213;
	const PRODUCT_SIZE_CHANGE_REQUEST = 214;
	const LONG_DELIVERY_DATE = 215;
	const ORDER_DELAY = 216;
	const NO_CAMPAIGN_GIFT_VOUCHER = 217;
	const PURCHASE_FROM_STORE = 218;
	const SHORT_COUNT_OF_INSTALLMENT = 219;
	const CHEAP_PRODUCT_DIFFERENT_SITE = 220;
	const FOUND_CHEAP_PRODUCT_YOUR_SITE = 221;
	const ORDER_UN_APPROVAL_CAC = 280;
	const ECP_CUSTOMER_REQUEST_CANCELLATION = 222;
	const DUPLICATE_PAYMENT = 223;
	const CANCELLED_CASH_ON_DELIVERY_ORDER = 224;
	const I_FORGOT_DISCOUNT = 225;
	const I_GAVE_UP = 226;
	const WRONG_ORDER = 91091;
	const WANT_CHANGE = 91092;
	const WRONG_ADDRESS = 227;
	const IVR_CANCEL = 228;
	const CHATBOT_CANCEL = 229;
	const MARKETPLACE_CANCELLED = 232; // MarketplaceCancelled
	const DAMAGE_PRODUCT = 233; // Damaged Product

	/**
	 * @return array Массив с статусами.
	 */
	public static function getAll()
	{
		return [
			self::NOT_SET => \Yii::t('stock/titles', 'Не указано'),
			self::UNABLE_TO_FULFIL => \Yii::t('stock/titles', 'Нет на стоке (stock-adjustment)'),
			self::UN_COLLECTED => \Yii::t('stock/titles', 'Есть в стоке но не нашли(stock-adjustment)'),
			self::DAMAGE_PRODUCT => \Yii::t('stock/titles', 'В заказе бракованный товар'),
//			self::ON_TRANSFER_B2C_TO_B2B => \Yii::t('stock/titles', 'товары которые в трансфере B2C TO B2B'),  //
			'xxxxxx' => 'xxxxxxxxxxxxxx-xxxxxxxxxxxxxx-xxxxxxxxxxxxxx',  //
			self::MARKETPLACE_CANCELLED => \Yii::t('stock/titles', 'Отмена для КАСПИ маркета'),
			self::CUSTOMER_REQUESTS_CANCELLATION => \Yii::t('stock/titles', 'Клиент отказался'),
			self::FRAUD => \Yii::t('stock/titles', 'Ошибочный заказ или обман'),
			self::WRONG_ADDRESS => \Yii::t('stock/titles', 'Неправильный адрес'),
			self::ENTER_CANCELLATION_REASON_MANUALLY => \Yii::t('stock/titles', 'Указываем руками причину отмены'),
		];
	}

	public static function getAllEN()
	{
		return [
			self::NOT_SET => \Yii::t('stock/titles', 'NOT-SET'),
			self::UNABLE_TO_FULFIL => \Yii::t('stock/titles', 'UNABLE-TO-FULFIL'),
			self::UN_COLLECTED => \Yii::t('stock/titles', 'UN-COLLECTED'),
			self::DAMAGE_PRODUCT => \Yii::t('stock/titles', 'DAMAGE-PRODUCT'),
//			self::ON_TRANSFER_B2C_TO_B2B => \Yii::t('stock/titles', 'ON-TRANSFER-B2C-TO-B2B'),  //
			self::CUSTOMER_REQUESTS_CANCELLATION => \Yii::t('stock/titles', 'CUSTOMER-REQUESTS-CANCELLATION'),
			self::FRAUD => \Yii::t('stock/titles', 'FRAUD'),
			self::ENTER_CANCELLATION_REASON_MANUALLY => \Yii::t('stock/titles', 'ENTER-CANCELLATION-REASON-MANUALLY'),
		];
	}

	/**
	 * @return array Массив с статусами.
	 */
	public static function getForCancelAll()
	{
		$list = self::getAll();

		unset($list[self::CUSTOMER_REQUESTS_CANCELLATION]);
		return $list;
	}

	/**
	 * @return array Массив с статусами.
	 */
	public static function getForPartReReservedList()
	{
		return [
			self::UN_COLLECTED => 'Не нашли в адресе',
//            self::UN_COLLECTED => 'Есть в стоке но не нашли',
//			self::DAMAGE_PRODUCT => 'Бракованный товар',
//			self::ON_TRANSFER_B2C_TO_B2B => 'В трасфере',
		];
	}

	/**
	 * @param string $status Читабельный статус поста.
	 * @param string $aLanguage Язык.
	 * @return string
	 */
	public static function getValue($status = null, $aLanguage = 'RU')
	{
		$list = $aLanguage == 'EN' ? self::getAllEN() : self::getAll();

		return ArrayHelper::getValue($list, $status);
	}

	/**
	 * @param string $status Читабельный статус поста.
	 * @return string
	 */
	public static function getValueForPartReReserved($status = null)
	{
		return ArrayHelper::getValue(self::getForPartReReservedList(), $status);
	}
}