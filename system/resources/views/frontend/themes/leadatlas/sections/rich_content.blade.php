@php
    $data = $section->data;
@endphp
<section class="spb-section pt-12 md:pt-14" data-anim>
    <div class="container">
        @if(!empty($data['notice']))
            <div class="legal__notice" data-anim-item>
                <i class="ph ph-warning-circle" aria-hidden="true"></i>
                <p>{{ $data['notice'] }}</p>
            </div>
        @endif

        <div class="legal" data-anim-item>
            {!! $data['content'] ?? '' !!}
        </div>
    </div>
</section>
