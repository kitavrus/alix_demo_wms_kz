# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

WMS (Warehouse Management System) for Kazakhstan (NOMADEX / alix_demo_wms_kz). Built on **Yii2 Advanced Application Template** with PHP. The system manages warehouse operations: inbound, outbound, stock, ecommerce integration, Kaspi marketplace integration, and transport logistics.

## Development Environment

### Docker (recommended)
```shell
# Start all services
docker-compose up --build -d

# Stop
docker-compose down
```

Services after startup:
- **Port 8080** → `stockDepartment/web/` (main WMS interface)
- **Port 8081** → `clientDepartment/web/` (client portal)
- **Port 8082** → `masterDepartment/web/d-u-m-p-e-r/` (DB dumper tool)
- **Port 8085** → Adminer (DB admin UI, default server: `db`)
- **MySQL** → `localhost:33060`, database: `alix_wms`, user/password: `user/password`

### Environment initialization
Before first run, initialize the environment (copies local config files):
```shell
php init
```
Choose `Development` or `Production`.

### Database migrations
```shell
php yii migrate
```

## Application Architecture

### Multi-department structure
The app is split into independent "department" applications, each with its own `web/`, `config/`, `controllers/`, `views/`:

| Directory | Purpose |
|---|---|
| `stockDepartment/` | Main WMS: warehouse staff operations |
| `clientDepartment/` | Client-facing portal |
| `masterDepartment/` | Admin/master tools |
| `console/` | CLI commands (cron, sync, migrations) |
| `common/` | Shared models, components, entities, config |
| `environments/` | Per-environment config overrides (dev/prod/local) |

### Config merging pattern
Each department merges configs in order:
1. `common/config/main.php` + `common/config/main-local.php`
2. `{department}/config/main.php` + `{department}/config/main-local.php`

The `*-local.php` files (gitignored) contain environment-specific DB credentials and secrets. The `environments/` directory provides templates for these local files.

### Two DB connections
Defined in `common/config/main-local.php`:
- `db` — main application database
- `dbAudit` — separate audit log database

### Modules (stockDepartment)
Modules are registered in `stockDepartment/config/main.php`. Key modules:

| Module key | Class/path | Purpose |
|---|---|---|
| `alix` | `stockDepartment\modules\alix` | Intermode B2B API (inbound/outbound/stock), ecommerce scanning workflows |
| `ecommerce` | `stockDepartment\modules\ecommerce` | Defacto & Intermode ecommerce operations |
| `kaspi` | `stockDepartment\modules\kaspi` | Kaspi marketplace integration |
| `inbound` / `outbound` | `app\modules\inbound` / `outbound` | Core WMS operations |
| `stock` | `app\modules\stock` | Stock management |
| `wms` / `tms` | `app\modules\wms` / `tms` | WMS/TMS core |

### Core data models (in `common/`)
- `common/modules/stock/models/Stock.php` — legacy stock table (`stock`)
- `common/ecommerce/entities/EcommerceStock.php` — current ecommerce stock table (`ecommerce_stock`); migration from `stock` to `EcommerceStock` is in progress (branch `sergey/move-to-ecommerce-stock`)
- `common/ecommerce/entities/EcommerceInbound.php`, `EcommerceOutbound.php` — ecommerce order entities
- `common/ecommerce/constants/` — shared status constants used across all modules

### Returns flow (important)
Kaspi / Alix ecommerce returns are modeled as **inbound orders**, not as a separate return entity:
- Header: `InboundOrder` (`inbound_orders`) with `order_type = InboundOrder::ORDER_TYPE_ECOMM_RETURN`
- Lines: `InboundOrderItem` (`inbound_order_items`)
- Physical scans per box: `EcommerceStock` (`ecommerce_stock`), joined by `inbound_id`

The tables `ecommerce_return` and `ecommerce_return_items` (and models `EcommerceReturn*`) are **legacy DeFacto-only** schema — their columns carry DeFacto-specific `client_*` API fields. They are still read/written by `common/ecommerce/defacto/returnOutbound/service/ReturnService.php` and `stockDepartment/modules/ecommerce/controllers/defacto/ReportController.php`, but **do not use them for new Kaspi/Alix returns work** — use `InboundOrder` with `ORDER_TYPE_ECOMM_RETURN`.

### Client constants
- `Client::CLIENT_ERENRETAIL = 103` — historical name; under id=103 the DB now has `AlixAvien`.
- `Client::CLIENT_ALIXAVIEN` — alias for the same id=103, used in the new Kaspi/ecommerce-returns flow. `CLIENT_ERENRETAIL` is kept for backward compatibility with legacy code in `modules/wms/controllers/erenRetail/`, `modules/intermode_/`, etc.

### Domain structure inside `alix` module
Business logic follows a layered pattern inside controllers:
```
controllers/
  ecommerce/
    outbound/
      domain/
        constants/     ← status enums
        dto/           ← request/response data transfer objects
        entities/      ← ActiveRecord models (thin wrappers)
        repository/    ← DB queries
        service/       ← business logic
        mapper/        ← DTO ↔ entity mapping
        validation/    ← input validation
```

### API routes (stockDepartment)
Custom URL rules defined in `stockDepartment/config/main.php`:
- `POST alix/api/v1/stock` → Intermode B2B stock API
- `POST alix/api/v1/inbound/orders` → B2B inbound orders
- `POST alix/api/v1/outbound/orders` → B2B outbound orders
- `POST alix/ecommerce/api/v1/outbound/orders` → Ecommerce outbound orders
- `POST alix/ecommerce/api/v1/outbound/cancel` → Cancel ecommerce outbound
- `POST alix/api/v1/inbound/returns` → Inbound returns

### Console commands
Located in `console/controllers/`. Run via:
```shell
php yii {controller}/{action}
```
Key controllers: `CronController`, `SyncController`, `IntermodeController`, `InventoryController`.

## Testing

Uses Codeception. Each department has its own test suites:
```shell
# Run unit tests for stockDepartment
php vendor/bin/codecept run unit -c stockDepartment/codeception.yml

# Run a single test file
php vendor/bin/codecept run unit path/to/TestFile.php -c stockDepartment/codeception.yml
```

## Key conventions

- **Namespace pattern**: `stockDepartment\modules\{module}\...` for stock-dept-specific code; `app\modules\{module}\...` for shared modules; `common\ecommerce\...` for shared ecommerce logic.
- **Currency**: KZT (Kazakhstan Tenge). Formatter configured in `common/config/main.php`.
- **Timezone**: `Asia/Almaty` for display, `UTC` for storage.
- **Language**: Russian (`ru`). i18n messages in `common/messages/`.
- Files with date suffixes (e.g. `Controller-24-07-2020.php`) are old versions kept for reference — do not modify them.
- Files suffixed `_NOT_USED` are deprecated — do not use.
