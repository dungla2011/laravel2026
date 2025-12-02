# 🌍 Hướng Dẫn Sử Dụng Đa Ngôn Ngữ với Optional Locale Prefix

## 📋 Tổng quan

Hệ thống hỗ trợ URL với **optional locale prefix**:

```
Tiếng Việt (mặc định):
https://abc.com/login
https://abc.com/register
https://abc.com/buy-vip

English:
https://abc.com/en/login
https://abc.com/en/register
https://abc.com/en/buy-vip

Japanese:
https://abc.com/ja/login
```

---

## ✅ Files đã tạo/cập nhật

### 1. **Routes**
- ✅ `routes/web_auth_i18n.php` - Routes với optional locale prefix
- 📝 Copy nội dung vào `routes/web_auth.php` để áp dụng

### 2. **Helpers**
- ✅ `app/Helpers/locale_helpers.php` - Helper functions
- ✅ `composer.json` - Đã thêm autoload

### 3. **Components**
- ✅ `resources/views/components/language-switcher.blade.php` - Language selector dropdown

### 4. **Middleware**
- ⚠️ Cần cập nhật `app/Http/Middleware/SetLocale.php`

---

## 🔧 Cài đặt

### Bước 1: Copy routes

Copy nội dung từ `routes/web_auth_i18n.php` → `routes/web_auth.php`

### Bước 2: Run composer

```bash
cd /e/Projects/laravel2022-01/laravel01
composer dump-autoload
```

### Bước 3: Update Middleware SetLocale

Sửa file `app/Http/Middleware/SetLocale.php`:

```php
public function handle(Request $request, Closure $next)
{
    $locale = null;

    // 1. From URL parameter (HIGHEST PRIORITY)
    $routeLocale = $request->route('locale');
    if ($routeLocale && in_array($routeLocale, \clang1::getLanguageListKey())) {
        $locale = $routeLocale;
        Session::put('locale', $locale);
    }

    // 2. User's saved preference (if logged in)
    if (!$locale && auth()->check() && !empty(auth()->user()->language)) {
        $locale = auth()->user()->language;
    }

    // 3. Session locale
    if (!$locale && Session::has('locale')) {
        $locale = Session::get('locale');
    }

    // 4. Default from config
    if (!$locale) {
        $locale = config('app.locale', 'vi');
    }

    // Validate
    $supportedLanguages = \clang1::getLanguageListKey();
    if (!in_array($locale, $supportedLanguages)) {
        $locale = 'vi';
    }

    App::setLocale($locale);
    
    if (class_exists('\Carbon\Carbon')) {
        \Carbon\Carbon::setLocale($locale);
    }
    
    view()->share('currentLocale', $locale);

    return $next($request);
}
```

### Bước 4: Thêm Language Switcher vào Header

Trong file header/navbar của bạn (vd: `header-all.blade.php`):

```blade
<!-- Thêm Language Switcher -->
@include('components.language-switcher')
```

---

## 💡 Cách Sử Dụng Helper Functions

### 1. **Lấy locale hiện tại**

```php
$locale = current_locale();  // 'vi', 'en', 'ja', ...
$default = default_locale(); // 'vi'
```

### 2. **Generate URL với locale**

```php
// Automatic (dùng locale hiện tại)
$url = localized_url('login');
// /login (nếu vi)
// /en/login (nếu en)

// Specify locale
$url = localized_url('login', [], 'en');   // /en/login
$url = localized_url('login', [], 'vi');   // /login
$url = localized_url('buy-vip', ['id' => 5], 'ja'); // /ja/buy-vip?id=5
```

### 3. **Chuyển đổi ngôn ngữ (giữ nguyên trang)**

```php
$url = switch_locale('en');  // Trang hiện tại nhưng đổi sang English
$url = switch_locale('vi');  // Trang hiện tại nhưng đổi sang Tiếng Việt
```

### 4. **Check locale**

```php
if (is_current_locale('vi')) {
    echo 'Đang dùng Tiếng Việt';
}
```

### 5. **Get locale info**

```php
$name = locale_name('vi');    // 'Tiếng Việt'
$flag = locale_flag('en');    // '🇺🇸'
$list = supported_locales();  // ['vi', 'en', 'ja', 'ko', ...]
```

---

## 🎨 Usage trong Blade Templates

### Tạo links với locale

```blade
{{-- Auto detect current locale --}}
<a href="{{ localized_url('login') }}">{{ __('auth.login') }}</a>
<a href="{{ localized_url('register') }}">{{ __('auth.register') }}</a>

{{-- Force specific locale --}}
<a href="{{ localized_url('buy-vip', [], 'en') }}">Buy VIP (EN)</a>
<a href="{{ localized_url('buy-vip', [], 'ja') }}">VIP購入 (JA)</a>

{{-- With parameters --}}
<a href="{{ localized_url('product.show', ['id' => 123], 'en') }}">
    Product #123 (English)
</a>
```

### Language Switcher Manual

```blade
<div class="language-selector">
    @foreach(supported_locales() as $locale)
        <a href="{{ switch_locale($locale) }}" 
           class="{{ is_current_locale($locale) ? 'active' : '' }}">
            {{ locale_flag($locale) }}
            {{ locale_name($locale) }}
        </a>
    @endforeach
</div>
```

### Conditional rendering by locale

```blade
@if(current_locale() === 'vi')
    <p>Nội dung chỉ hiển thị cho người Việt</p>
@elseif(current_locale() === 'en')
    <p>Content only for English users</p>
@endif
```

---

## 🔄 Route Name Convention

Tất cả routes giữ nguyên tên, **không cần** `.localized`:

```php
// ✅ Đúng:
route('login')                           // /login hoặc /en/login
route('login', ['locale' => 'en'])       // /en/login
route('auth.register', ['locale' => 'ja']) // /ja/register

// ❌ Sai (không cần .localized):
route('login.localized')
```

---

## 📊 URL Examples

| Current Locale | Route Call | Generated URL |
|---------------|-----------|--------------|
| `vi` | `route('login')` | `/login` |
| `en` | `route('login')` | `/en/login` |
| `vi` | `route('login', ['locale' => 'en'])` | `/en/login` |
| `en` | `route('login', ['locale' => 'vi'])` | `/login` |
| `ja` | `route('buy-vip')` | `/ja/buy-vip` |

---

## 🧪 Testing

### Test URLs:

```
# Tiếng Việt (default)
https://yourdomain.com/login
https://yourdomain.com/register
https://yourdomain.com/reset-password

# English
https://yourdomain.com/en/login
https://yourdomain.com/en/register
https://yourdomain.com/en/reset-password

# Japanese
https://yourdomain.com/ja/login
```

### Test trong Controller:

```php
public function test() {
    dd([
        'current_locale' => current_locale(),
        'login_url' => route('login'),
        'login_en' => route('login', ['locale' => 'en']),
        'switch_to_ja' => switch_locale('ja'),
    ]);
}
```

---

## 🎯 Best Practices

### 1. **Luôn dùng helper functions**

```blade
{{-- ✅ Đúng --}}
<a href="{{ localized_url('login') }}">Login</a>

{{-- ❌ Sai --}}
<a href="/login">Login</a>
```

### 2. **Language switcher giữ nguyên trang**

```blade
{{-- ✅ Đúng: Giữ nguyên trang hiện tại --}}
<a href="{{ switch_locale('en') }}">English</a>

{{-- ❌ Sai: Chuyển về trang chủ --}}
<a href="{{ localized_url('home', [], 'en') }}">English</a>
```

### 3. **Form action URLs**

```blade
<form action="{{ localized_url('post.login') }}" method="POST">
    @csrf
    {{-- form fields --}}
</form>
```

---

## 🐛 Troubleshooting

### Vấn đề: Vẫn hiển thị English dù set default là 'vi'

**Nguyên nhân:** Middleware chưa chạy hoặc routes chưa có middleware

**Giải pháp:**
1. Check `routes/web_auth.php` có `->middleware(['setlocale'])` không
2. Clear cache: `php artisan route:clear && php artisan cache:clear`

### Vấn đề: Helper function không tồn tại

**Nguyên nhân:** Composer chưa autoload

**Giải pháp:**
```bash
composer dump-autoload
```

### Vấn đề: Language switcher không giữ trang hiện tại

**Nguyên nhân:** Dùng sai helper

**Giải pháp:** Dùng `switch_locale()` thay vì `localized_url()`

---

## ✅ Checklist Hoàn thành

- [ ] Copy `web_auth_i18n.php` → `web_auth.php`
- [ ] Run `composer dump-autoload`
- [ ] Update Middleware SetLocale
- [ ] Thêm Language Switcher vào header
- [ ] Test các URL với locale prefix
- [ ] Clear cache routes

---

**✨ Hoàn thành! Hệ thống đã sẵn sàng cho đa ngôn ngữ!** 🎉
