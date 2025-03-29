# My Custom Real Estate Manager

**My Custom Real Estate Manager** — це плагін для WordPress, який додає можливість керування об'єктами нерухомості через кастомний тип записів, REST API, AJAX-фільтрацію та XML-імпорт.

---

## 📁 Основні можливості

- Кастомний тип запису `estate`
- Таксономія `district`
- Поля через ACF для будівель та приміщень
- AJAX-фільтр та пагінація результатів
- REST API (GET, POST, PUT, DELETE)
- XML імпорт з URL
- Віджет фільтра нерухомості
- Підтримка i18n (перекладів)

---

## 🚧 Встановлення

1. Завантажити плагін у директорію `/wp-content/plugins/my-custom-real-estate/`
2. Активувати плагін у WordPress
3. Переконатись, що встановлений та активований [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/)
4. Використати шорткод `[real_estate_filter id="shortcode"]` або додати віджет у розділі Вигляд > Віджети

---

## 🛋️ REST API

### ✅ POST `/wp-json/real-estate/v1/objects`

**Опис**: Створення нового об'єкта нерухомості

**Тіло (JSON):**
```json
{
  "title": "Будинок №1",
  "estate_name": "ЖК Сонячний",
  "coords": "49.8397, 24.0297",
  "eco_rating": 4,
  "floors_count": 5,
  "building_type": "цегла",
  "district": "sychiv",
  "estate_image": "https://example.com/image.jpg",
  "rooms": [
    {
      "area": "55 м²",
      "room_count": "2",
      "balcony": "так",
      "wc": "так",
      "room_image": "https://example.com/room1.jpg"
    }
  ]
}
```

---

### ✏️ PUT `/wp-json/real-estate/v1/objects/{id}`

Оновлення існуючого об'єкта за ID. Формат тіла такий же, як у POST.

---

### ❌ DELETE `/wp-json/real-estate/v1/objects/{id}`

Видалення об'єкта за ID.

---

### 📋 GET `/wp-json/real-estate/v1/objects`

Отримати всі об'єкти. Можливі параметри:
- `district`
- `eco_rating`
- `floors_count`

**Приклад:**
```
/wp-json/real-estate/v1/objects?district=sychiv&eco_rating=3
```

---

## 💾 XML Імпорт

### ✅ POST `/wp-json/real-estate/v1/import-xml`

Імпортує об'єкти нерухомості з XML файлу по URL.

**Тіло (JSON):**
```json
{
  "url": "https://example.com/feed.xml"
}
```

**Формат XML:**
```xml
<estates>
  <estate>
    <title>Будинок №1</title>
    <name>ЖК Сонячний</name>
    <coords>49.8397, 24.0297</coords>
    <eco_rating>4</eco_rating>
    <floors_count>5</floors_count>
    <building_type>цегла</building_type>
    <district>sychiv</district>
    <estate_image>https://example.com/image.jpg</estate_image>
    <rooms>
      <room>
        <area>55 м²</area>
        <room_count>2</room_count>
        <balcony>так</balcony>
        <wc>так</wc>
        <room_image>https://example.com/room1.jpg</room_image>
      </room>
    </rooms>
  </estate>
</estates>
```

---

## 🌍 Шорткод

```
[real_estate_filter]
```

Підтримує AJAX-фільтрацію і пагінацію, окремий ID дозволяє уникнути конфліктів із віджетом.

---

## 📏 Віджет

Можна вставити в будь-яку область віджетів через інтерфейс WordPress. Виводить той самий фільтр, що й шорткод.

---

## 🌐 Переклад

- Text Domain: `realestate`
- Підтримка `__()` та `_e()` функцій
- JavaScript рядки передаються через `wp_localize_script`
