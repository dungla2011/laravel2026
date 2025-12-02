# 🌍 I18N (Internationalization) Guide

## 📋 Tổng quan hệ thống đa ngôn ngữ

Project này hỗ trợ **đa ngôn ngữ đầy đủ** với 3 tầng:

### **Tầng 1: Laravel Built-in Localization**
- File: `resources/lang/{locale}/*.php`
- Dùng cho: Validation messages, auth messages, pagination
- Syntax: `{{ __('auth.failed') }}` hoặc `{{ trans('auth.failed') }}`

### **Tầng 2: Database Translations**
- Tables: `model_meta_infos.translations`, `menu_tree.translations`
- Dùng cho: Dynamic content (menu, field labels)
- Syntax: `{{ trans_field('user_name') }}`, `{{ trans_menu(1) }}`

### **Tầng 3: User Language Preference**
- Table: `users.language`
- Auto-detect: Browser → Session → User DB → Config default

---

## 🚀 Cách sử dụng trong Code

### **1. Trong Blade Templates:**

```blade
{{-- Laravel built-in trans --}}
<h1>{{ __('Welcome') }}</h1>
<p>{{ __('auth.failed') }}</p>

{{-- Database field translation --}}
<label>{{ trans_field('user_name') }}</label>
<label>{{ trans_field('email') }}</label>

{{-- Database menu translation --}}
<a href="#">{{ trans_menu('dashboard') }}</a>
<a href="#">{{ trans_menu(5) }}</a>

{{-- Current locale --}}
<html lang="{{ current_locale() }}" dir="{{ is_rtl() ? 'rtl' : 'ltr' }}">

{{-- Flag icon --}}
<i class="{{ flag_icon('vi') }}"></i>
```

### **2. Trong Controllers/PHP:**

```php
use App\Helpers\TranslationHelper;

// Get translated field
$label = trans_field('user_name'); // "User Name" or "Tên người dùng"

// Get translated menu
$menuName = trans_menu(1); // From database

// Get current locale
$locale = current_locale(); // 'en', 'vi', etc.

// Change locale programmatically
App::setLocale('vi');

// Get all languages
$languages = get_languages(); 
// ['en' => 'English', 'vi' => 'Tiếng Việt', ...]
```

### **3. Trong JavaScript/AJAX:**

```javascript
// Change user language (already implemented in header-all.blade.php)
$.post('/api/user/language', { language: 'vi' }, function(data) {
    location.reload(); // Reload to apply new language
});

// Get current locale from data attribute
let locale = document.documentElement.lang; // 'en', 'vi', etc.
```

---

## 🔧 Workflow thêm ngôn ngữ mới

### **Step 1: Thêm vào clang1 class**

File: `app/common.php`

```php
class clang1 {
    public static $enableLanguage = [
        'en' => 'English',
        'vi' => 'Tiếng Việt',
        'zh' => '中文',  // Thêm Chinese
        'ja' => '日本語', // Thêm Japanese
        // ... add more
    ];
    
    public static $flagMap = [
        'en' => 'us',
        'vi' => 'vn',
        'zh' => 'cn',  // Thêm flag
        'ja' => 'jp',  // Thêm flag
        // ... add more
    ];
}
```

### **Step 2: Tạo folder language files**

```bash
mkdir resources/lang/zh
mkdir resources/lang/ja

# Copy từ English làm template
cp resources/lang/en/*.php resources/lang/zh/
cp resources/lang/en/*.php resources/lang/ja/
```

### **Step 3: Dịch nội dung**

Edit `resources/lang/zh/fields.php`, `resources/lang/ja/fields.php`, etc.

### **Step 4: Thêm translations vào database**

Dùng UI editors:
- Menu translations: `/tool/common/menu_translation_editor.php`
- Field translations: `/tool/common/language_edit_fields.php`

---

## 📝 Best Practices

### **1. Khi nào dùng Laravel trans() vs trans_field()**

| Use Case | Function | Example |
|----------|----------|---------|
| Static text (validation, auth) | `__()` or `trans()` | `{{ __('auth.failed') }}` |
| Dynamic field labels | `trans_field()` | `{{ trans_field('user_name') }}` |
| Dynamic menu names | `trans_menu()` | `{{ trans_menu(1) }}` |
| Custom messages | `__()` | `{{ __('messages.welcome', ['name' => $user]) }}` |

### **2. Tổ chức translation keys**

```php
// ❌ BAD: Hardcode text
<label>User Name</label>

// ✅ GOOD: Use trans_field for database-driven labels
<label>{{ trans_field('user_name') }}</label>

// ✅ GOOD: Use __ for static text
<p>{{ __('Please enter your details') }}</p>
```

### **3. Fallback strategy**

System tự động fallback theo thứ tự:
1. Database translation (nếu có)
2. Laravel lang file (nếu có)
3. Humanized field name (last resort)

```php
// VD: trans_field('product_name')
// 1. Check DB: model_meta_infos.translations['vi']['product_name']
// 2. Check file: resources/lang/vi/fields.php['product_name']
// 3. Fallback: "Product Name" (humanized)
```

---

## 🛠️ Advanced Usage

### **1. Custom locale per request**

```php
// Override locale for specific user
trans_field('user_name', 'zh'); // Force Chinese

// Get menu in different language
trans_menu(1, 'ja'); // Force Japanese
```

### **2. RTL Language Support**

```blade
<html dir="{{ is_rtl() ? 'rtl' : 'ltr' }}">
<body class="{{ is_rtl() ? 'rtl-layout' : '' }}">
```

### **3. Date/Time Localization**

```php
// Carbon automatically uses current locale (set in SetLocale middleware)
$date = \Carbon\Carbon::now()->translatedFormat('l, d F Y');
// English: "Monday, 6 October 2025"
// Vietnamese: "Thứ hai, 6 Tháng mười 2025"
```

### **4. Currency Formatting**

```php
// Use PHP NumberFormatter
$formatter = new NumberFormatter(current_locale(), NumberFormatter::CURRENCY);
echo $formatter->formatCurrency(1234.56, 'VND');
// English: "₫1,234.56"
// Vietnamese: "1.234,56 ₫"
```

---

## 📊 Database Structure

### **model_meta_infos table:**

```sql
CREATE TABLE model_meta_infos (
    id INT PRIMARY KEY,
    field VARCHAR(255),
    translations JSON,  -- {"en": "User Name", "vi": "Tên người dùng"}
    ...
);
```

### **menu_tree table:**

```sql
CREATE TABLE menu_tree (
    id INT PRIMARY KEY,
    name VARCHAR(255),
    translations JSON,  -- {"en": "Dashboard", "vi": "Bảng điều khiển"}
    ...
);
```

### **users table:**

```sql
CREATE TABLE users (
    id INT PRIMARY KEY,
    language VARCHAR(10),  -- 'en', 'vi', 'zh', etc.
    ...
);
```

---

## 🎯 Testing Checklist

- [ ] Middleware SetLocale hoạt động (check `App::getLocale()`)
- [ ] Language selector trong navbar hoạt động
- [ ] User language preference được lưu vào DB
- [ ] Menu translations hiển thị đúng
- [ ] Field labels hiển thị đúng
- [ ] Validation messages đúng ngôn ngữ
- [ ] Date/time format đúng locale
- [ ] RTL languages (Arabic, Hebrew) render đúng

---

## 🐛 Troubleshooting

### **Locale không đổi:**

```bash
# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Regenerate autoload
composer dump-autoload
```

### **Trans không hoạt động:**

```bash
# Check current locale
php artisan tinker
>>> App::getLocale()

# Check if middleware registered
php artisan route:list --middleware=web
```

### **Database translations trống:**

- Kiểm tra JSON column có data không: `SELECT translations FROM menu_tree`
- Dùng UI editor để thêm translations: `/tool/common/menu_translation_editor.php`

---

## 📚 Resources

- **Laravel Localization**: https://laravel.com/docs/localization
- **Flag Icons**: https://flagicons.lipis.dev/
- **Carbon Localization**: https://carbon.nesbot.com/docs/#api-localization
- **PHP Intl Extension**: https://www.php.net/manual/en/book.intl.php

---

## ✅ Hoàn thành!

Hệ thống i18n của bạn đã sẵn sàng! 🎉

- ✅ Auto-detect user language
- ✅ Database-driven translations
- ✅ Helper functions
- ✅ UI editors
- ✅ 60+ languages supported
