@props([
    'action' => url()->current(),
    'periode' => request('periode', 'hari_ini'),
    'id' => 'periodFilterForm_' . uniqid(),
])

@php
    $validPeriods = [
        'hari_ini' => 'Hari Ini',
        'minggu_ini' => 'Minggu Ini',
        'bulan_ini' => 'Bulan Ini',
        'semua' => 'Semua',
    ];
    $currentVal = array_key_exists($periode, $validPeriods) ? $periode : 'hari_ini';
    $currentLabel = $validPeriods[$currentVal];
    $uniqueId = preg_replace('/[^a-zA-Z0-9]/', '', $id);
@endphp

<div class="relative shrink-0" id="periodDropdownWrap_{{ $uniqueId }}">
    <form method="GET" action="{{ $action }}" id="{{ $id }}" class="m-0">
        {{-- Preserve existing query parameters except periode and page --}}
        @foreach(request()->except(['periode', 'page']) as $key => $val)
            @if(is_array($val))
                @foreach($val as $subVal)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $subVal }}">
                @endforeach
            @elseif(!is_null($val))
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endif
        @endforeach
        <input type="hidden" name="periode" id="periodInput_{{ $uniqueId }}" value="{{ $currentVal }}">
    </form>

    <!-- Compact Custom Dropdown Trigger Button -->
    <button type="button"
        id="periodBtn_{{ $uniqueId }}"
        onclick="togglePeriodDropdown('{{ $uniqueId }}', event)"
        aria-haspopup="true"
        aria-expanded="false"
        class="h-8 sm:h-9 inline-flex items-center gap-1 sm:gap-1.5 px-2 sm:px-2.5 bg-slate-100/90 hover:bg-slate-200/80 active:scale-95 border border-slate-200/90 rounded-xl text-[11px] sm:text-xs font-bold text-slate-700 transition-all cursor-pointer shadow-2xs select-none">
        <svg class="w-3.5 h-3.5 text-orange-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
        </svg>
        <span class="truncate max-w-[72px] sm:max-w-none">{{ $currentLabel }}</span>
        <svg class="w-3 h-3 text-slate-400 transition-transform duration-200 shrink-0" id="periodArrow_{{ $uniqueId }}" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    <!-- Sleek Compact Dropdown Menu (Matches Header Scale & Font Size) -->
    <div id="periodMenu_{{ $uniqueId }}"
        class="hidden absolute right-0 mt-1.5 w-32 sm:w-36 bg-white border border-slate-200 rounded-xl shadow-lg shadow-slate-900/10 p-1 z-50 transition-all transform origin-top-right">
        @foreach($validPeriods as $val => $label)
            <button type="button"
                onclick="selectPeriod('{{ $uniqueId }}', '{{ $val }}')"
                class="w-full text-left px-2.5 py-1.5 rounded-lg text-xs font-semibold flex items-center justify-between transition-colors cursor-pointer {{ $currentVal === $val ? 'bg-orange-50 text-orange-600 font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                <span>{{ $label }}</span>
                @if($currentVal === $val)
                <svg class="w-3.5 h-3.5 text-orange-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                @endif
            </button>
        @endforeach
    </div>
</div>

@once
@push('scripts')
<script>
    function togglePeriodDropdown(uniqueId, event) {
        if (event) event.stopPropagation();
        var menu = document.getElementById('periodMenu_' + uniqueId);
        var arrow = document.getElementById('periodArrow_' + uniqueId);
        var btn = document.getElementById('periodBtn_' + uniqueId);
        if (!menu) return;

        var isHidden = menu.classList.contains('hidden');
        // Close all period menus first
        document.querySelectorAll('[id^="periodMenu_"]').forEach(function(m) {
            m.classList.add('hidden');
        });
        document.querySelectorAll('[id^="periodArrow_"]').forEach(function(a) {
            a.classList.remove('rotate-180');
        });

        if (isHidden) {
            menu.classList.remove('hidden');
            if (arrow) arrow.classList.add('rotate-180');
            if (btn) btn.setAttribute('aria-expanded', 'true');
        } else {
            menu.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        }
    }

    function selectPeriod(uniqueId, val) {
        var input = document.getElementById('periodInput_' + uniqueId);
        var form = document.getElementById('periodFilterForm_' + uniqueId) || input?.form;
        if (input && form) {
            input.value = val;
            form.submit();
        }
    }

    // Close on click outside or Escape
    document.addEventListener('click', function(e) {
        if (!e.target.closest('[id^="periodDropdownWrap_"]')) {
            document.querySelectorAll('[id^="periodMenu_"]').forEach(function(m) {
                m.classList.add('hidden');
            });
            document.querySelectorAll('[id^="periodArrow_"]').forEach(function(a) {
                a.classList.remove('rotate-180');
            });
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id^="periodMenu_"]').forEach(function(m) {
                m.classList.add('hidden');
            });
            document.querySelectorAll('[id^="periodArrow_"]').forEach(function(a) {
                a.classList.remove('rotate-180');
            });
        }
    });
</script>
@endpush
@endonce
