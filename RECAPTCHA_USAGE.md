# reCAPTCHA v3 Integration Guide

## Quick Start

### 1. Add to Form
Add these attributes to your form:
```blade
<form data-recaptcha="true" data-recaptcha-action="register">
    @csrf
    
    <!-- Your form fields -->
    
    <!-- Hidden input for reCAPTCHA token -->
    <input type="hidden" name="g-recaptcha-response">
    
    <button type="submit">Submit</button>
</form>
```

### 2. Include reCAPTCHA Script
At the end of your view (before `@endsection`):
```blade
@include('partials.recaptcha_v3', ['action' => 'register'])
```

### 3. Backend Validation
In your controller:
```php
public function register(Request $request)
{
    // Verify reCAPTCHA
    if (!$this->verifyRecaptcha($request->input('g-recaptcha-response'))) {
        return back()->withErrors([
            'recaptcha' => __('auth.recaptcha_failed')
        ])->withInput();
    }
    
    // Continue with your logic...
}

private function verifyRecaptcha($recaptchaResponse)
{
    if (empty($recaptchaResponse)) {
        return false;
    }
    
    $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
        'secret' => config('recaptcha.api_secret_key'),
        'response' => $recaptchaResponse,
        'remoteip' => request()->ip()
    ]);
    
    $result = $response->json();
    
    if (isset($result['success']) && $result['success'] === true) {
        if (isset($result['score'])) {
            $threshold = config('recaptcha.score_threshold', 0.5);
            return $result['score'] >= $threshold;
        }
        return true;
    }
    
    return false;
}
```

## Complete Examples

### Example 1: Register Form
```blade
@section('content')
<form action="{{ route('auth.register') }}" method="post" data-recaptcha="true" data-recaptcha-action="register">
    @csrf
    
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    
    <!-- reCAPTCHA hidden input -->
    <input type="hidden" name="g-recaptcha-response">
    
    @error('recaptcha')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
    
    <button type="submit">Register</button>
</form>

@include('partials.recaptcha_v3', ['action' => 'register'])
@endsection
```

### Example 2: Login Form
```blade
@section('content')
<form action="{{ route('login') }}" method="post" data-recaptcha="true" data-recaptcha-action="login">
    @csrf
    
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    
    <!-- reCAPTCHA hidden input -->
    <input type="hidden" name="g-recaptcha-response">
    
    @error('recaptcha')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
    
    <button type="submit">Login</button>
</form>

@include('partials.recaptcha_v3', ['action' => 'login'])
@endsection
```

### Example 3: Contact Form
```blade
@section('content')
<form action="{{ route('contact.submit') }}" method="post" data-recaptcha="true" data-recaptcha-action="contact">
    @csrf
    
    <input type="text" name="name" required>
    <input type="email" name="email" required>
    <textarea name="message" required></textarea>
    
    <!-- reCAPTCHA hidden input -->
    <input type="hidden" name="g-recaptcha-response">
    
    @error('recaptcha')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
    
    <button type="submit">Send Message</button>
</form>

@include('partials.recaptcha_v3', ['action' => 'contact'])
@endsection
```

## Configuration

### Environment Variables
Add to `.env`:
```env
RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here
RECAPTCHA_SCORE_THRESHOLD=0.5
```

### Config File
`config/recaptcha.php`:
```php
return [
    'api_site_key' => env('RECAPTCHA_SITE_KEY'),
    'api_secret_key' => env('RECAPTCHA_SECRET_KEY'),
    'score_threshold' => env('RECAPTCHA_SCORE_THRESHOLD', 0.5),
];
```

## How It Works

1. **User submits form** → JavaScript intercepts the submit event
2. **Check for token** → If token exists, submit normally
3. **Get reCAPTCHA token** → Call `grecaptcha.execute()` to get invisible token
4. **Set token to hidden input** → `g-recaptcha-response` field
5. **Submit form** → With token included
6. **Backend validates** → Verify token with Google API
7. **Check score** → For v3, score must be >= threshold (default 0.5)

## Features

- ✅ **Easy to use**: Just add 2 attributes + 1 include
- ✅ **Reusable**: One partial file for all forms
- ✅ **Multiple forms**: Support multiple forms on same page
- ✅ **Custom actions**: Each form can have different action name
- ✅ **No layout dependency**: Works without `@yield('js')` in layout
- ✅ **Invisible**: reCAPTCHA v3 runs in background
- ✅ **Score-based**: Flexible threshold configuration

## Troubleshooting

### Token is empty on backend
- Check browser Console for JavaScript errors
- Verify `data-recaptcha="true"` is on form
- Ensure hidden input `name="g-recaptcha-response"` exists
- Check Network tab for reCAPTCHA API call

### Verification always fails
- Verify RECAPTCHA_SECRET_KEY in `.env`
- Check domain is registered in Google reCAPTCHA admin
- For localhost, add to allowed domains
- Run `php artisan config:cache` after changing .env

### Score too low
- Adjust `RECAPTCHA_SCORE_THRESHOLD` (0.0 to 1.0)
- Lower value = less strict (e.g., 0.3)
- Higher value = more strict (e.g., 0.7)
- Default 0.5 is recommended

## Support

For issues or questions, check:
- Google reCAPTCHA Admin: https://www.google.com/recaptcha/admin
- reCAPTCHA Documentation: https://developers.google.com/recaptcha/docs/v3
