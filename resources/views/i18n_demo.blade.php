<!DOCTYPE html>
<html lang="{{ current_locale() }}" dir="{{ is_rtl() ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('I18N Demo') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/7.2.3/css/flag-icons.min.css">
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 50px auto; padding: 20px; }
        h1 { color: #333; }
        .demo-section { margin: 30px 0; padding: 20px; background: #f5f5f5; border-radius: 8px; }
        .demo-section h2 { color: #007bff; margin-top: 0; }
        .code { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 5px; overflow-x: auto; font-family: monospace; }
        .result { background: #fff; padding: 15px; margin-top: 10px; border-left: 4px solid #28a745; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table td, table th { padding: 10px; border: 1px solid #ddd; text-align: left; }
        table th { background: #007bff; color: white; }
        .flag-icon { font-size: 1.5em; margin-right: 5px; }
        .lang-switcher { margin: 20px 0; }
        .lang-switcher button { margin: 5px; padding: 10px 20px; cursor: pointer; border: 2px solid #007bff; background: white; border-radius: 5px; }
        .lang-switcher button:hover { background: #007bff; color: white; }
        .lang-switcher button.active { background: #28a745; color: white; border-color: #28a745; }
    </style>
</head>
<body>
    <h1>🌍 I18N (Internationalization) Demo</h1>

    <div class="demo-section">
        <h2>Current Locale Information</h2>
        <div class="code">
// Current locale: {{ current_locale() }}
// Is RTL: {{ is_rtl() ? 'Yes' : 'No' }}
// Available languages: {{ count(get_languages()) }}
        </div>
        <div class="result">
            <p><strong>Current Locale:</strong> {{ current_locale() }}</p>
            <p><strong>Is RTL:</strong> {{ is_rtl() ? 'Yes' : 'No' }}</p>
            <p><strong>Available Languages:</strong> {{ count(get_languages()) }}</p>
        </div>
    </div>

    <div class="demo-section">
        <h2>Language Switcher</h2>
        <div class="lang-switcher">
            @foreach(get_languages() as $code => $name)
                <button onclick="changeLanguage('{{ $code }}')" class="{{ current_locale() == $code ? 'active' : '' }}">
                    <i class="flag-icon {{ flag_icon($code) }}"></i>
                    {{ $name }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="demo-section">
        <h2>1. Laravel Built-in Trans (Static Files)</h2>
        <div class="code">
&lt;!-- Blade Syntax --&gt;
{{ '{{ __("auth.failed") }}' }}
{{ '{{ __("validation.required") }}' }}
{{ '{{ trans("passwords.reset") }}' }}
        </div>
        <div class="result">
            <p><strong>auth.failed:</strong> {{ __('auth.failed') }}</p>
            <p><strong>validation.required:</strong> {{ __('validation.required') }}</p>
            <p><strong>passwords.reset:</strong> {{ trans('passwords.reset') }}</p>


        </div>
    </div>

    <div class="demo-section">
        <h2>2. Database Field Translations (Dynamic)</h2>
        <div class="code">
&lt;!-- Blade Syntax --&gt;
{{ '{{ trans_field("user_name") }}' }}
{{ '{{ trans_field("email") }}' }}
{{ '{{ trans_field("password") }}' }}

        </div>
        <div class="result">
            <table>
                <thead>
                    <tr>
                        <th>Field Name</th>
                        <th>Translated Label</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>user_name</td>
                        <td>{{ trans_field('user_name') }}</td>
                    </tr>
                    <tr>
                        <td>email</td>
                        <td>{{ trans_field('email') }}</td>
                    </tr>
                    <tr>
                        <td>password</td>
                        <td>{{ trans_field('password') }}</td>
                    </tr>
                    <tr>
                        <td>phone</td>
                        <td>{{ trans_field('phone') }}</td>
                    </tr>
                    <tr>
                        <td>full_name</td>
                        <td>{{ trans_field('full_name') }}</td>
                    </tr>
                    <tr>
                        <td>address</td>
                        <td>{{ trans_field('address') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="demo-section">
        <h2>3. Database Menu Translations (Dynamic)</h2>
        <div class="code">
&lt;!-- Blade Syntax --&gt;
{{ '{{ trans_menu("dashboard") }}' }}
{{ '{{ trans_menu(1) }}' }}
        </div>
        <div class="result">
            <p><strong>Menu by name:</strong> {{ trans_menu('dashboard') }}</p>
            <p><strong>Menu by ID:</strong> {{ trans_menu(1) }}</p>
        </div>
    </div>

    <div class="demo-section">
        <h2>4. All Available Languages</h2>
        <div class="code">
&lt;?php
foreach(get_languages() as $code => $name) {
    echo "$code => $name\n";
}
?&gt;
        </div>
        <div class="result">
            <table>
                <thead>
                    <tr>
                        <th>Flag</th>
                        <th>Code</th>
                        <th>Language Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(get_languages() as $code => $name)
                    <tr>
                        <td><i class="flag-icon {{ flag_icon($code) }}"></i></td>
                        <td><code>{{ $code }}</code></td>
                        <td>{{ $name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="demo-section">
        <h2>5. Date/Time Localization (Carbon)</h2>
        <div class="code">
&lt;?php
$date = \Carbon\Carbon::now();
echo $date->translatedFormat('l, d F Y');
echo $date->diffForHumans();
?&gt;
        </div>
        <div class="result">
            <p><strong>Full Date:</strong> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
            <p><strong>Relative Time:</strong> {{ \Carbon\Carbon::now()->diffForHumans() }}</p>
            <p><strong>Short Format:</strong> {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="demo-section">
        <h2>6. Form Example (Multi-language)</h2>
        <div class="result">
            <form>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: bold;">{{ trans_field('user_name') }}:</label>
                    <input type="text" placeholder="{{ trans_field('user_name') }}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: bold;">{{ trans_field('email') }}:</label>
                    <input type="email" placeholder="{{ trans_field('email') }}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: bold;">{{ trans_field('password') }}:</label>
                    <input type="password" placeholder="{{ trans_field('password') }}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <button type="button" style="padding: 10px 30px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    {{ trans_field('submit') }}
                </button>
            </form>
        </div>
    </div>

    <script>
        function changeLanguage(locale) {
            // Send AJAX request to change user language
            fetch('/api/user/language', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ language: locale })
            })
            .then(response => response.json())
            .then(data => {
                // Reload page to apply new language
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                // Fallback: use session
                document.cookie = `locale=${locale}; path=/`;
                location.reload();
            });
        }
    </script>
</body>
</html>
