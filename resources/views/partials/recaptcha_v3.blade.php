{{-- Google reCAPTCHA v3 
    Usage: @include('partials.recaptcha_v3', ['action' => 'register'])
    Form must have: <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
--}}

<script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.api_site_key') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[data-recaptcha="true"]');
    
    forms.forEach(function(form) {
        const submitButton = form.querySelector('input[type="submit"], button[type="submit"]');
        const action = form.getAttribute('data-recaptcha-action') || '{{ $action ?? 'submit' }}';
        
        form.addEventListener('submit', function(e) {
            const tokenInput = form.querySelector('input[name="g-recaptcha-response"]');
            
            // If token already exists, let form submit
            if (tokenInput && tokenInput.value) {
                return true;
            }
            
            // No token yet, get it first
            e.preventDefault();
            
            if (submitButton) {
                submitButton.disabled = true;
                const originalValue = submitButton.value || submitButton.textContent;
                if (submitButton.tagName === 'INPUT') {
                    submitButton.value = '{{ __("auth.processing") ?? "Processing..." }}';
                } else {
                    submitButton.textContent = '{{ __("auth.processing") ?? "Processing..." }}';
                }
            }
            
            grecaptcha.ready(function() {
                grecaptcha.execute('{{ config('recaptcha.api_site_key') }}', {action: action})
                    .then(function(token) {
                        if (tokenInput) {
                            tokenInput.value = token;
                        }
                        form.submit();
                    })
                    .catch(function(error) {
                        console.error('reCAPTCHA error:', error);
                        alert('{{ __("auth.recaptcha_error") ?? "reCAPTCHA verification failed. Please try again!" }}');
                        
                        if (submitButton) {
                            submitButton.disabled = false;
                            if (submitButton.tagName === 'INPUT') {
                                submitButton.value = originalValue;
                            } else {
                                submitButton.textContent = originalValue;
                            }
                        }
                    });
            });
        });
    });
});
</script>
