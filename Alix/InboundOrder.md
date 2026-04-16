# API документация: Приход товара и Kaspi интеграция

## Содержание

- [Аутентификация](#аутентификация)
- [Приход товара (Inbound)](#приход-товара-inbound)

---

## Аутентификация

### Inbound API (`https://alix-demo-wms.nmdx.kz/alix/api/v1/...`)

Bearer-токен — поле `auth_key` из таблицы `user`.

```
Authorization: Bearer <auth_key>
```

Один из вариантов:
- Заголовок `Authorization: Bearer <auth_key>`
- Авторизованная сессия (cookie)

---

## Приход товара (Inbound)

Процесс по схеме **1С → Nomadex → 1С**:

```
1С: Поступление ТМЗ  --API-->  Nomadex: InBound order (NEW)
                                         ↓
                                Приёмка на склад (SCANNING → SCANNED)
                                         ↓
                                InBound order (CONFIRM)  --API-->  1С: Приходный ордер
```

### Создание прихода

```
POST /alix/api/v1/inbound/orders
```

**Заголовки:**
```
Content-Type: application/json
Authorization: Bearer <auth_key>
```

**Тело запроса:**
```json
{
  "order_id": "ORDER-001",
  "1с_uuid": "unique-uuid-from-1c",
  "comment": "Поступление ТМЗ от поставщика",
  "items": [
    {
      "barcode": "8682538223186",
      "article": "1100014950",
      "guid": "e8a98110-2cf0-11f1-9759-ac1f6b8140a5",
      "quantity": 10
    },
    {
      "barcode": "8682538208367",
      "article": "1100004567",
      "guid": "0a8f1d8e-3133-11f1-975b-ac1f6b8140a5",
      "quantity": 5
    }
  ]
}
```

| Поле | Тип | Обязательно | Описание |
|------|-----|-------------|----------|
| `order_id` | string | да | Номер заказа |
| `1с_uuid` | string | да | Уникальный UUID из 1С (для дедупликации) |
| `comment` | string | нет | Комментарий к заказу |
| `items` | array | да | Массив товаров (минимум 1) |
| `items[].barcode` | string | да | Штрих-код товара |
| `items[].article` | string | да | Артикул товара |
| `items[].guid` | string | да | GUID товара |
| `items[].quantity` | int | да | Ожидаемое количество |

**Успешный ответ (200):**
```json
{
  "status": "success",
  "message": "",
  "code": "",
  "wms_id": 123
}
```

**Ошибка валидации (400):**
```json
{
  "status": "error",
  "message": "Описание ошибки",
  "code": "",
  "wms_id": ""
}
```

**Примечания:**
- Товарные данные (name, brand, color) подтягиваются из номенклатуры
- При повторной отправке того же `1с_uuid` в статусе NEW — старые items удаляются и создаются новые
- При повторной отправке того же `1с_uuid` НЕ в статусе NEW — вернётся ошибка
