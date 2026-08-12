{{--
    Google reCAPTCHA v2 widget for forms. Renders the challenge widget and
    loads the reCAPTCHA script only when the plugin is enabled and a site key
    is configured. Place inside a <form>; it adds the g-recaptcha-response
    field the server verifies.
--}}
@php
    $recaptchaRenderable = \App\Support\RecaptchaConfig::renderable();
    $recaptchaSiteKey = \App\Support\RecaptchaConfig::siteKey();
@endphp

@if($recaptchaRenderable)
    <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
    @error('g-recaptcha-response')
        <p class="form-error">{{ $message }}</p>
    @enderror
    @once
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endonce
@endif
