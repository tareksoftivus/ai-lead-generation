{{-- Favicon --}}
@if(setting('site_favicon') && media_url(setting('site_favicon')))
<link rel="icon" href="{{ media_url(setting('site_favicon')) }}">
@else
<link rel="icon" type="image/png" href="{{ asset('assets/uploads/brand/leadatlas-favicon.png') }}">
@endif
