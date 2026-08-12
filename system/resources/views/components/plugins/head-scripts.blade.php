{{--
    Third-party plugin scripts for public-facing pages (frontend + guest/auth).
    Rendered only when the corresponding plugin is enabled in Settings → Plugins.
--}}
@php
    $ga4Enabled = (bool) setting('plugin_ga4_enabled', false);
    $ga4Id = trim((string) setting('plugin_ga4_measurement_id', ''));

    $tawkEnabled = (bool) setting('plugin_tawk_enabled', false);
    $tawkProperty = trim((string) setting('plugin_tawk_property_id', ''));
    $tawkWidget = trim((string) setting('plugin_tawk_widget_id', 'default')) ?: 'default';
@endphp

@if($ga4Enabled && $ga4Id !== '')
    {{-- Google Analytics 4 --}}
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
    <script>
        'use strict';
        window.dataLayer = window.dataLayer || [];
        function gtag(){window.dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', <?php echo json_encode($ga4Id, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>);
    </script>
@endif

@if($tawkEnabled && $tawkProperty !== '')
    {{-- Tawk.to Live Chat --}}
    <script>
        'use strict';
        var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
        (function () {
            'use strict';
            var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/' + <?php echo json_encode($tawkProperty, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?> + '/' + <?php echo json_encode($tawkWidget, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>
@endif
