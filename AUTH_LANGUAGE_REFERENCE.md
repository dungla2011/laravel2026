# 🌐 Tham Chiếu Ngôn Ngữ Đăng Nhập/Đăng Ký

## 📋 Danh Sách Các Key Ngôn Ngữ

| Key | Tiếng Việt | English |
|-----|------------|---------|
| `auth.login` | Đăng nhập | Login |
| `auth.register` | Đăng ký | Register |
| `auth.logout` | Đăng xuất | Logout |
| `auth.email_or_account` | Email hoặc Tài khoản | Email or Account |
| `auth.username` | Tên tài khoản (viết liền gồm chữ và số, dấu gạch dưới) | Username (alphanumeric and underscore only) |
| `auth.account` | Tài khoản | Account |
| `auth.password_field` | Mật khẩu | Password |
| `auth.password_confirmation` | Nhập lại Mật khẩu | Confirm Password |
| `auth.forgot_password` | Quên mật khẩu | Forgot Password |
| `auth.activate_account` | Kích hoạt tài khoản | Activate Account |
| `auth.home` | Trang chủ | Home |
| `auth.activation_message` | Nhập email đã đăng ký để kích hoạt tài khoản (nếu tài khoản đã đăng ký mà chưa nhận được mail kích hoạt) | Enter your registered email to activate account (if you registered but haven't received the activation email) |
| `auth.enter_email` | Nhập địa chỉ email | Enter email address |
| `auth.email` | Email | Email |
| `auth.remember_me` | Ghi nhớ đăng nhập | Remember Me |
| `auth.submit` | Gửi | Submit |
| `auth.back_to_login` | Quay lại đăng nhập | Back to Login |

---

## 💡 Cách Sử Dụng Trong Blade Template

### Ví dụ 1: Hiển thị label
```blade
<label>{{ __('auth.email_or_account') }}</label>
<input type="text" name="email" placeholder="{{ __('auth.enter_email') }}">
```

### Ví dụ 2: Button
```blade
<button type="submit">{{ __('auth.login') }}</button>
<a href="/register">{{ __('auth.register') }}</a>
```

### Ví dụ 3: Form đầy đủ
```blade
<form method="POST" action="/login">
    @csrf
    
    <div class="form-group">
        <label>{{ __('auth.email_or_account') }}</label>
        <input type="text" name="email" placeholder="{{ __('auth.enter_email') }}" required>
    </div>
    
    <div class="form-group">
        <label>{{ __('auth.password_field') }}</label>
        <input type="password" name="password" placeholder="{{ __('auth.password_field') }}" required>
    </div>
    
    <div class="form-check">
        <input type="checkbox" name="remember" id="remember">
        <label for="remember">{{ __('auth.remember_me') }}</label>
    </div>
    
    <button type="submit">{{ __('auth.login') }}</button>
    
    <a href="/forgot-password">{{ __('auth.forgot_password') }}</a>
    <a href="/register">{{ __('auth.register') }}</a>
</form>
```

### Ví dụ 4: Trang kích hoạt tài khoản
```blade
<h2>{{ __('auth.activate_account') }}</h2>
<p>{{ __('auth.activation_message') }}</p>

<form method="POST" action="/activate">
    @csrf
    <input type="email" name="email" placeholder="{{ __('auth.enter_email') }}" required>
    <button type="submit">{{ __('auth.submit') }}</button>
</form>

<a href="/login">{{ __('auth.back_to_login') }}</a>
```

---

## 🔄 Đổi Ngôn Ngữ

### Trong Controller hoặc Route
```php
// Đổi sang tiếng Việt
App::setLocale('vi');

// Đổi sang tiếng Anh
App::setLocale('en');
```

### Trong Middleware
```php
// app/Http/Middleware/SetLocale.php
public function handle($request, Closure $next)
{
    $locale = session('locale', 'vi'); // Mặc định tiếng Việt
    App::setLocale($locale);
    return $next($request);
}
```

### Cho phép user chọn ngôn ngữ
```php
Route::get('/language/{locale}', function ($locale) {
    if (in_array($locale, ['vi', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
});
```

---

## 📂 Vị Trí Files

- **Tiếng Việt**: `resources/lang/vi/auth.php`
- **Tiếng Anh**: `resources/lang/en/auth.php`

---

## ✅ Checklist Migration

Để thay thế hardcoded text sang sử dụng translation:

- [ ] Form đăng nhập (`/login`)
- [ ] Form đăng ký (`/register`)
- [ ] Form quên mật khẩu (`/forgot-password`)
- [ ] Form reset mật khẩu (`/reset-password`)
- [ ] Trang kích hoạt tài khoản
- [ ] Navigation menu (Login/Logout links)
- [ ] Email templates
- [ ] Flash messages
- [ ] Validation messages

---

## 🎯 Best Practices

1. **Luôn dùng `__()` helper**: `{{ __('auth.login') }}`
2. **Không hardcode text**: Tránh `<button>Đăng nhập</button>`
3. **Nhất quán key naming**: Dùng snake_case cho keys
4. **Group theo module**: `auth.*`, `validation.*`, `passwords.*`
5. **Document rõ ràng**: Ghi chú context khi cần thiết

---

**✨ File này giúp bạn dễ dàng tham khảo và áp dụng đa ngôn ngữ cho hệ thống authentication!**
