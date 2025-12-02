# Internationalization (i18n) Logic Documentation

## Overview

Hệ thống đa ngôn ngữ của application sử dụng **URL-based locale** với user preference fallback.

---

## Locale Priority (Highest → Lowest)

### 1. 🔗 URL Locale Parameter (HIGHEST PRIORITY)
```
/en/pricing → English
/ja/login   → Japanese
/fr/about   → French
```

**Lý do:** User **chủ động chọn** xem ngôn ngữ này (qua URL)

**Behavior:**
- Tất cả links trong page giữ nguyên locale: `/ja/home`, `/ja/pricing`, etc.
- Language switcher hiển thị cờ tương ứng với URL locale
- Override user's saved preference

### 2. 👤 User's Saved Language (if logged in)
```php
$user->language = 'en'
/pricing → English (không có URL locale, dùng user preference)
```

**Behavior:**
- Chỉ áp dụng khi URL **KHÔNG có** locale prefix
- Persist across sessions
- Can be changed via language switcher

### 3. 💾 Session Locale
```php
Session::get('locale') = 'ko'
```

**Behavior:**
- Remember last selected language
- Used when user not logged in AND no URL locale

### 4. ⚙️ Config Default
```php
config('app.locale') = 'vi' // Vietnamese
```

**Fallback cuối cùng**

---

## Use Cases & Behaviors

### Case 1: Guest User
```
URL: /ja/pricing
→ app()->getLocale() = 'ja'
→ All links: /ja/home, /ja/about
→ Language switcher shows JP active
```

### Case 2: User (language=EN) navigates to Japanese
```
user->language = 'en'
URL: /ja/pricing

→ app()->getLocale() = 'ja' (URL priority!)
→ All links: /ja/home, /ja/about
→ Can switch back to EN via language switcher
```

### Case 3: User (language=EN) on default URL
```
user->language = 'en'
URL: /pricing (no locale)

→ app()->getLocale() = 'en' (from user preference)
→ All links: /en/home, /en/about
```

### Case 4: First-time guest
```
URL: /pricing
→ app()->getLocale() = 'vi' (default)
→ All links: /home, /about (no prefix for default)
```

---

## URL Structure

### Default Locale (vi)
```
/                 → Vietnamese
/pricing          → Vietnamese
/about            → Vietnamese
```

### Other Locales
```
/en/              → English
/en/pricing       → English
/ja/login         → Japanese
/fr/about         → French
```

---

## Helper Functions

### `localized_route($name, $params = [])`
Generate route with current locale prefix

```blade
{{-- Current locale: ja --}}
<a href="{{ localized_route('home') }}">
    {{-- Output: /ja/ --}}
</a>

<a href="{{ localized_route('pricing') }}">
    {{-- Output: /ja/pricing --}}
</a>

{{-- Current locale: vi (default) --}}
<a href="{{ localized_route('home') }}">
    {{-- Output: / --}}
</a>
```

### `switch_locale($locale)`
Get current page URL with different locale

```blade
{{-- Current: /ja/pricing?abc=1 --}}
<a href="{{ switch_locale('en') }}">
    {{-- Output: /en/pricing?abc=1 --}}
</a>

<a href="{{ switch_locale('vi') }}">
    {{-- Output: /pricing?abc=1 --}}
</a>
```

### `get_locale_name($locale = null)`
Get full language name

```php
get_locale_name('en') // "English"
get_locale_name('ja') // "日本語"
get_locale_name()     // Current locale name
```

---

## Language Switcher Behavior

### For Guests (not logged in)
```
Click language → Navigate to URL with new locale
Example: /pricing → Click EN → /en/pricing
```

### For Logged-in Users
```
Click language → 
1. AJAX save to user->language
2. Navigate to URL with new locale

Example: 
- On /ja/pricing
- Click EN
- Save user->language = 'en'
- Redirect to /en/pricing
```

---

## Route Registration Pattern

All routes are registered twice for locale support:

```php
// Without prefix (default locale)
Route::get('/pricing', [Controller::class, 'pricing'])
    ->name('pricing');

// With locale prefix
Route::prefix('{locale}')
    ->where(['locale' => 'en|ja|ko|fr|de|es|zh'])
    ->group(function() {
        Route::get('/pricing', [Controller::class, 'pricing'])
            ->name('pricing.localized');
    });
```

---

## Middleware: SetLocale

**File:** `app/Http/Middleware/SetLocale.php`

**Logic:**
```php
1. Check URL locale → Use it (highest priority)
2. Check user->language → Use it (if logged in)
3. Check session → Use it (fallback)
4. Use config default → 'vi'
```

**Applied to:** `web` middleware group

---

## Translation Files

```
resources/lang/
├── vi/
│   ├── auth.php        # Login, register, etc.
│   ├── validation.php
│   └── ...
├── en/
│   ├── auth.php
│   └── ...
├── ja/
└── ...
```

**Usage:**
```blade
{{ __('auth.login') }}          # "Đăng nhập" or "Login"
{{ __('auth.email_field') }}    # "Email" or "メール"
```

---

## Why URL Priority?

### ✅ Advantages
1. **SEO Friendly:** Each language has unique URL
2. **Shareable:** Share `/ja/pricing` → Everyone sees Japanese
3. **Multi-language Browsing:** Open EN and JA in different tabs
4. **Testing:** Developers can test all languages easily
5. **User Control:** Explicit choice via URL

### ❌ Why NOT redirect?
```php
// BAD: Auto-redirect based on user preference
user->language = 'en'
/ja/pricing → REDIRECT → /en/pricing ❌

Problems:
- User can't view other languages
- Shared links break
- SEO issues
- Extra server load
```

---

## Summary

**Logic:** URL locale > User preference > Session > Default

**Philosophy:** Respect **explicit user choice** (URL) over implicit preference

**Implementation:** Clean, SEO-friendly, user-friendly

---

## See Also

- `LOCALE_SETUP_GUIDE.md` - Full setup instructions
- `app/Http/Middleware/SetLocale.php` - Middleware implementation
- `app/Helpers/locale_helpers.php` - Helper functions
- `routes/web_auth_i18n.php` - Example route registration
