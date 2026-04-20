Таймлайн — кто, когда, что создаёт

  Момент: Кaspi → APPROVED_BY_BANK
  Кто запускает: — (на стороне Kaspi, клиент оплатил)
  Что создаётся/меняется в БД: В нашей БД ничего.
  Заказ виден в scanning-form?: Нет.
  ────────────────────────────────────────
  Момент: Cron kaspi-poll-orders (раз в 15 мин)
  Кто запускает: CronController::actionKaspiPollOrders → OrderImportService::pollAndImportNew
  Что создаётся/меняется в БД: INSERT в ecommerce_outbound (external_order_number=<kaspiId>, external_kaspi_status=ACCEPTED_BY_MERCHANT),
    INSERT в ecommerce_outbound_items, UPDATE ecommerce_stock (status_availability=RESERVED, outbound_id=<id>).
  Заказ виден в scanning-form?: Нет.
  ────────────────────────────────────────
  Момент: Открытие заказа для сборки, pick list
  Кто запускает: Кладовщик в /alix/outbound/picking/... (PickingController::actionPrint)
  Что создаётся/меняется в БД: UPDATE stock.status = PRINTED_PICKING_LIST.
  Заказ виден в scanning-form?: Нет.
  ────────────────────────────────────────
  Момент: Сканирование товара в коробку
  Кто запускает: Кладовщик в /alix/outbound/scanning/scanning-form (ScanningController::actionProductBarcodeHandler →
    OutboundRepository::makeScannedStock, :290-304)
  Что создаётся/меняется в БД: UPDATE stock.box_barcode = <шк коробки>, status = SCANNED. Вот тут box_barcode получает значение.
  Заказ виден в scanning-form?: Нет.
  ────────────────────────────────────────
  Момент: Упаковка и «запаковать накладную»
  Кто запускает: actionPackage($orderNumber) → OutboundScanningService::package → repository->packageOrder
  Что создаётся/меняется в БД: UPDATE stock.status = PACKED.
  Заказ виден в scanning-form?: Нет, но готов к сканированию в отгрузочный лист.
  ────────────────────────────────────────
  Момент: Сканирование коробки в лист отгрузки
  Кто запускает: Кладовщик в /alix/outbound/outbound-list/scanning-form (эта форма) → OutboundListController::actionBarcode →
    OutboundListForm::Barcode validate → OutboundListService::scanPackageBarcode (OutboundListService.php:41-58)
  Что создаётся/меняется в БД: INSERT в ecommerce_outbound_list: list_title, package_barcode, courier_company, our_outbound_id и т.д.
  Заказ виден в scanning-form?: Да, именно тут появляется.