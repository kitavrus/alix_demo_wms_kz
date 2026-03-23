<?php

namespace stockDepartment\modules\kaspi\constants;

/**
 * @see https://guide.kaspi.kz/partner/ru/shop/api/goods
 * @see https://guide.kaspi.kz/partner/ru/shop/api/orders
 */
class KaspiConstants
{
    const BASE_URL = 'https://kaspi.kz/shop/api/v2/';
    const PRODUCTS_API_BASE_URL = 'https://kaspi.kz/shop/api/';
    const DEFAULT_USE_MOCK = true; // Поставить false для реальных запросов
    const DEFAULT_HTTP_LOG = false;
    const DEFAULT_ALLOW_GUEST_API = true;

    const CONTENT_TYPE_JSON_API = 'application/vnd.api+json';
    const CONTENT_TYPE_JSON = 'application/json';

    const LOG_CATEGORY = 'kaspi';
    const API_TOKEN_PLACEHOLDER = ''; //'+vWV5nZLFOVPEisce0YR9doMiBlv0NKfclVukFWP1SM=';

    const PRODUCTS_IMPORT_SCHEMA_PATH = 'products/import/schema';
    const PRODUCTS_IMPORT_PATH = 'products/import';
    const PRODUCTS_CLASSIFICATION_CATEGORIES = 'products/classification/categories';
    const PRODUCTS_CLASSIFICATION_ATTRIBUTES = 'products/classification/attributes';
    const PRODUCTS_CLASSIFICATION_ATTRIBUTE_VALUES = 'products/classification/attribute/values';

    const PRODUCTS_V2_STOCKS_ENDPOINT = 'products/update';
    const PRODUCTS_V2_OFFERS_ENDPOINT = 'offers';
    const PRODUCTS_V2_OFFERS_SCHEMA_ENDPOINT = 'offers/schema';
    const PRODUCTS_V2_CATEGORIES_ENDPOINT = 'categories';
    const PRODUCTS_V2_ATTRIBUTES_ENDPOINT = 'attributes';
    const PRODUCTS_V2_ATTRIBUTES_VALUES_PATH = 'attributes/values';
    const PRODUCTS_V2_OFFERS_STATUS_ENDPOINT = 'offers/status';

    const ORDERS_ENDPOINT = 'orders';
    const ORDER_ENTRY_SUBPATH = 'entries';
    const ORDER_WAYBILL_SUBPATH = 'waybill';
}
