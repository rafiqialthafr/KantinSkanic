@props([
    'title' => '',
    'value' => null,
    'sublabel' => null,
    'badge' => false,
    'color' => 'orange',
    'gradient' => null,
    'icon' => null,
    'isTimeSensitive' => false,
])

@php
    $gradients = [
        'orange'  => 'linear-gradient(135deg, #f97316, #ea580c)',
        'amber'   => 'linear-gradient(135deg, #f59e0b, #d97706)',
        'emerald' => 'linear-gradient(135deg, #059669, #10b981)',
        'sky'     => 'linear-gradient(135deg, #0284c7, #0369a1)',
        'indigo'  => 'linear-gradient(135deg, #6366f1, #4f46e5)',
        'purple'  => 'linear-gradient(135deg, #8b5cf6, #7c3aed)',
        'rose'    => 'linear-gradient(135deg, #f43f5e, #e11d48)',
        'slate'   => 'linear-gradient(135deg, #475569, #334155)',
    ];

    $bgStyle = $gradient ?? ($gradients[$color] ?? $gradients['orange']);
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl p-3.5 sm:p-5 relative overflow-hidden text-white shadow-2xs flex flex-col justify-between transition-all duration-200 hover:shadow-md hover:-translate-y-0.5 min-h-[130px] sm:min-h-[155px]']) }} style="background: {{ $bgStyle }};">
    {{-- Decorative subtle background circle --}}
    <div class="absolute -right-3 -top-3 w-20 h-20 rounded-full bg-white/10 pointer-events-none"></div>

    <div>
        {{-- Top Row: Icon + Optional Time-Sensitive indicator --}}
        <div class="flex items-center justify-between mb-2 sm:mb-2.5">
            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                @if(isset($icon))
                    {{ $icon }}
                @else
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                @endif
            </div>

            @if($isTimeSensitive)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/15 text-[9px] sm:text-[10px] font-bold text-white/95 backdrop-blur-xs tracking-tight shrink-0" title="Data diperbarui sesuai filter periode">
                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span>Periode</span>
                </span>
            @endif
        </div>

        {{-- Parameter Title --}}
        <p class="text-[10px] sm:text-xs font-bold text-white/80 uppercase tracking-wider line-clamp-1">
            {{ $title }}
        </p>

        {{-- Main Value --}}
        @php $isRupiah = is_string($value) && str_starts_with($value, 'Rp'); @endphp
        <div class="{{ $isRupiah ? 'text-base sm:text-xl lg:text-2xl font-black mt-0.5 sm:mt-1 leading-tight tracking-tight truncate' : 'text-xl sm:text-2xl lg:text-3xl font-black mt-0.5 sm:mt-1 leading-tight tracking-tight' }}" title="{{ is_string($value) ? $value : '' }}">
            @if($value !== null)
                {{ $value }}
            @else
                {{ $slot }}
            @endif
        </div>
    </div>

    {{-- Dynamic Sub-label --}}
    @if($sublabel)
        <div class="text-[10px] sm:text-xs text-white/90 mt-1.5 sm:mt-2 font-medium flex items-center gap-1.5 pt-1.5 border-t border-white/15">
            @if($badge)
                <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse shrink-0"></span>
            @endif
            <span class="truncate">{{ $sublabel }}</span>
        </div>
    @endif
</div>
