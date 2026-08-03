@php
    $data = $section->data;
    $planNames = $data['plan_names'] ?? [];
    $rows = $data['rows'] ?? [];
@endphp
<section class="spy-section bg-[#f6f5ff]" data-anim>
    <div class="container">
        <div class="sec-head sec-head--center" data-anim-item>
            @if(!empty($data['eyebrow']))
                <p class="sec-eyebrow">{{ $data['eyebrow'] }}</p>
            @endif
            <h2 class="sec-title">{{ $data['title'] ?? '' }}</h2>
        </div>

        <div class="mt-12 -mx-4 overflow-x-auto px-4 md:mt-14 md:mx-0 md:px-0" data-anim-item>
            <table class="cmp">
                <caption class="sr-only">
                    {{ __('Feature comparison across the :plans plans', ['plans' => implode(', ', array_column($planNames, 'name'))]) }}
                </caption>
                <thead>
                    <tr>
                        <th scope="col" class="w-[45%] text-left">{{ __('Feature') }}</th>
                        @foreach($planNames as $plan)
                            <th scope="col" @if(!empty($plan['highlighted'])) class="cmp__col--on" @endif>{{ $plan['name'] ?? '' }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        <tr>
                            <th scope="row" class="w-[45%] text-left">{{ $row['feature'] ?? '' }}</th>
                            @foreach(['starter', 'growth', 'scale'] as $index => $key)
                                @php $highlighted = !empty($planNames[$index]['highlighted']); @endphp
                                <td class="@if($highlighted) cmp__col--on @endif @if(($row['type'] ?? 'text') === 'text') numeric @endif">
                                    @if(($row['type'] ?? 'text') === 'check')
                                        @if(!empty($row[$key]))
                                            <i class="ph ph-check text-[1.125rem] text-success" aria-label="{{ __('Included') }}"></i>
                                        @else
                                            <i class="ph ph-minus text-[1.125rem] text-neutral-500" aria-label="{{ __('Not included') }}"></i>
                                        @endif
                                    @else
                                        {{ $row[$key] ?? '' }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
