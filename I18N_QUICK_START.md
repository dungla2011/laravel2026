# 🚀 I18N Quick Start Guide

## ✅ Đã setup xong! Bạn có thể dùng ngay.

### **1. Test ngay bằng Demo Page:**

```
URL: http://your-domain.com/i18n-demo
```

Hoặc chạy server local:

```bash
php artisan serve
# Mở: http://localhost:8000/i18n-demo
```

---

## 📝 Sử dụng trong 3 bước đơn giản:

### **Bước 1: Sử dụng trong Blade Template**

```blade
{{-- Cách 1: Laravel built-in (static text) --}}
<p>{{ __('Welcome to our website') }}</p>
<p>{{ __('auth.failed') }}</p>

{{-- Cách 2: Database field translations (dynamic) --}}
<label>{{ trans_field('user_name') }}</label>
<label>{{ trans_field('email') }}</label>

{{-- Cách 3: Database menu translations --}}
<a href="#">{{ trans_menu('dashboard') }}</a>
```

### **Bước 2: Sử dụng trong Controller/PHP**

```php
// Get translated text
$label = trans_field('user_name');
$menu = trans_menu(1);

// Get current locale
$locale = current_locale(); // 'en', 'vi', etc.

// Change locale
App::setLocale('vi');
```

### **Bước 3: Thêm translations qua UI**

```
Menu translations: /tool/common/menu_translation_editor.php
Field translations: /tool/common/language_edit_fields.php
```

---

## 🔧 Files quan trọng đã tạo:

| File | Mô tả |
|------|-------|
| `app/Http/Middleware/SetLocale.php` | Auto-detect user language |
| `app/Helpers/TranslationHelper.php` | Helper class cho translations |
| `app/Helpers/i18n_helpers.php` | Shorthand functions |
| `resources/lang/en/fields.php` | English field labels |
| `resources/lang/vi/fields.php` | Vietnamese field labels |
| `resources/views/i18n_demo.blade.php` | Demo page |
| `README_I18N.md` | Full documentation |

---

## 🎯 Test checklist:

- [x] Middleware đã đăng ký trong Kernel.php (web middleware group)
- [x] Helper functions đã autoload (composer dump-autoload)
- [x] Language files đã tạo (resources/lang/en, vi)
- [x] Database có JSON columns (translations)
- [x] UI editors sẵn sàng (/tool/common/)

---

## 📚 Đọc thêm:

- Full guide: `README_I18N.md`
- Demo page: `/i18n-demo`
- Laravel docs: https://laravel.com/docs/localization

---

## 🎉 Hoàn tất!

Hệ thống i18n của bạn đã sẵn sàng sử dụng!

Chỉ cần dùng:
- `{{ trans_field('field_name') }}` cho field labels
- `{{ trans_menu('menu_name') }}` cho menu names
- `{{ __('key') }}` cho static text

**Happy coding! 🚀**
