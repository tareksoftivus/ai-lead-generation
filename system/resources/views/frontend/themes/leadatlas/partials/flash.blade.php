@php
    $flashes = collect(['success', 'error', 'warning', 'info'])
        ->filter(fn ($type) => session()->has($type))
        ->map(fn ($type) => ['type' => $type, 'message' => session($type)]);
@endphp

@if($flashes->isNotEmpty())
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    @foreach($flashes as $flash)
    window.showToast(<?php echo json_encode(ucfirst($flash['type']), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>, <?php echo json_encode($flash['message'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>, <?php echo json_encode($flash['type'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>);
    @endforeach
});
</script>
@endif
