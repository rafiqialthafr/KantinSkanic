@props([
    'action' => url()->current(),
    'periode' => null,
    'id' => 'periodFilterForm_' . uniqid(),
])

@php
    $resolved = \App\Services\PeriodFilterService::resolve(request());
    $isCustom = $resolved['isCustom'];
    $currentVal = $periode ?? $resolved['periode'];
    $startDateStr = $resolved['startDateStr'];
    $endDateStr = $resolved['endDateStr'];
    $currentLabel = $resolved['periodeLabel'];
    $shortLabel = $resolved['shortLabel'];
    $periodeDetail = $resolved['periodeDetail'];
    $uniqueId = preg_replace('/[^a-zA-Z0-9]/', '', $id);

    $validPresets = [
        'hari_ini' => 'Hari Ini',
        'minggu_ini' => 'Minggu Ini',
        'bulan_ini' => 'Bulan Ini',
        'semua' => 'Semua',
    ];
@endphp

<div class="relative shrink-0" id="periodDropdownWrap_{{ $uniqueId }}">
    <form method="GET" action="{{ $action }}" id="{{ $id }}" class="m-0">
        {{-- Preserve existing query parameters except period filters and pagination --}}
        @foreach(request()->except(['periode', 'start_date', 'end_date', 'page']) as $key => $val)
            @if(is_array($val))
                @foreach($val as $subVal)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $subVal }}">
                @endforeach
            @elseif(!is_null($val))
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endif
        @endforeach
        <input type="hidden" name="periode" id="periodInput_{{ $uniqueId }}" value="{{ $currentVal }}">
        <input type="hidden" name="start_date" id="periodStartDateInput_{{ $uniqueId }}" value="{{ $startDateStr }}">
        <input type="hidden" name="end_date" id="periodEndDateInput_{{ $uniqueId }}" value="{{ $endDateStr }}">
    </form>

    <!-- Compact Custom Dropdown Trigger Button -->
    <button type="button"
        id="periodBtn_{{ $uniqueId }}"
        onclick="togglePeriodDropdown('{{ $uniqueId }}', event)"
        aria-haspopup="true"
        aria-expanded="false"
        title="Periode: {{ $currentLabel }} ({{ $periodeDetail }})"
        class="h-8 sm:h-9 inline-flex items-center gap-1 sm:gap-1.5 px-2 sm:px-2.5 {{ $isCustom ? 'bg-orange-50/95 border-orange-300 text-orange-700 shadow-2xs ring-1 ring-orange-500/20' : 'bg-slate-100/90 hover:bg-slate-200/80 border-slate-200/90 text-slate-700' }} active:scale-95 border rounded-xl text-[11px] sm:text-xs font-bold transition-all cursor-pointer shadow-2xs select-none">
        <svg class="w-3.5 h-3.5 {{ $isCustom ? 'text-orange-600' : 'text-orange-500' }} shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
        </svg>
        <span class="truncate max-w-[80px] sm:max-w-none">{{ $shortLabel }}</span>
        <svg class="w-3 h-3 text-slate-400 transition-transform duration-200 shrink-0" id="periodArrow_{{ $uniqueId }}" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    <!-- Sleek Custom Dropdown Menu with Date Range Picker -->
    <div id="periodMenu_{{ $uniqueId }}"
        class="hidden absolute right-0 mt-1.5 w-72 sm:w-80 bg-white border border-slate-200/90 rounded-2xl shadow-xl shadow-slate-900/10 p-3 z-50 transition-all transform origin-top-right">

        <!-- Header: Title & Reset Shortcut -->
        <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100">
            <span class="text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-slate-400">Pilih Periode Waktu</span>
            @if($isCustom)
                <button type="button"
                    onclick="selectPeriod('{{ $uniqueId }}', 'hari_ini')"
                    class="text-[11px] font-bold text-orange-600 hover:text-orange-700 hover:underline cursor-pointer">
                    Reset Hari Ini
                </button>
            @endif
        </div>

        <!-- Preset Buttons (Grid 2x2) -->
        <div class="grid grid-cols-2 gap-1.5 mb-3">
            @foreach($validPresets as $val => $label)
                @php $isSelected = (! $isCustom && $currentVal === $val); @endphp
                <button type="button"
                    onclick="selectPeriod('{{ $uniqueId }}', '{{ $val }}')"
                    class="w-full text-left px-2.5 py-1.5 rounded-xl text-xs font-semibold flex items-center justify-between transition-all cursor-pointer {{ $isSelected ? 'bg-orange-500 text-white font-bold shadow-xs shadow-orange-500/20' : 'text-slate-700 bg-slate-50 hover:bg-slate-100 hover:text-slate-900' }}">
                    <span>{{ $label }}</span>
                    @if($isSelected)
                    <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    @endif
                </button>
            @endforeach
        </div>

        <!-- Custom Date Range Section -->
        <div class="pt-2.5 border-t border-slate-100">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-orange-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                    <span>Filter Sesuai Tanggal</span>
                </span>
                @if($isCustom)
                    <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-md border border-emerald-200/60">Aktif</span>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-2 mb-2.5">
                <div>
                    <label for="startDatePicker_{{ $uniqueId }}" class="block text-[10px] font-bold text-slate-500 mb-1">Dari Tanggal</label>
                    <input type="date"
                        id="startDatePicker_{{ $uniqueId }}"
                        value="{{ $startDateStr ?? '' }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-1.5 text-xs text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all cursor-pointer">
                </div>
                <div>
                    <label for="endDatePicker_{{ $uniqueId }}" class="block text-[10px] font-bold text-slate-500 mb-1">Sampai Tanggal</label>
                    <input type="date"
                        id="endDatePicker_{{ $uniqueId }}"
                        value="{{ $endDateStr ?? '' }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-1.5 text-xs text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all cursor-pointer">
                </div>
            </div>

            <p id="dateValidationMsg_{{ $uniqueId }}" class="text-[11px] text-rose-600 font-semibold mb-2 hidden"></p>

            <button type="button"
                onclick="applyCustomDate('{{ $uniqueId }}')"
                class="w-full py-2 px-3 rounded-xl bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-bold text-xs shadow-md shadow-orange-500/20 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                <span>Terapkan Filter Tanggal</span>
            </button>
        </div>
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
        // Close all other period menus first
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
        var startInput = document.getElementById('periodStartDateInput_' + uniqueId);
        var endInput = document.getElementById('periodEndDateInput_' + uniqueId);
        var form = document.getElementById('periodFilterForm_' + uniqueId) || input?.form;

        if (input && form) {
            input.value = val;
            if (startInput) startInput.value = '';
            if (endInput) endInput.value = '';
            form.submit();
        }
    }

    function applyCustomDate(uniqueId) {
        var startPicker = document.getElementById('startDatePicker_' + uniqueId);
        var endPicker = document.getElementById('endDatePicker_' + uniqueId);
        var input = document.getElementById('periodInput_' + uniqueId);
        var startInput = document.getElementById('periodStartDateInput_' + uniqueId);
        var endInput = document.getElementById('periodEndDateInput_' + uniqueId);
        var form = document.getElementById('periodFilterForm_' + uniqueId) || input?.form;
        var msg = document.getElementById('dateValidationMsg_' + uniqueId);

        var startVal = startPicker ? startPicker.value.trim() : '';
        var endVal = endPicker ? endPicker.value.trim() : '';

        if (!startVal && !endVal) {
            if (msg) {
                msg.textContent = 'Silakan pilih tanggal terlebih dahulu.';
                msg.classList.remove('hidden');
            }
            if (startPicker) startPicker.focus();
            return;
        }

        // If only start date is provided, filter for that single day
        if (startVal && !endVal) {
            endVal = startVal;
        } else if (!startVal && endVal) {
            startVal = endVal;
        }

        // If start date is after end date, swap them automatically
        if (startVal > endVal) {
            var temp = startVal;
            startVal = endVal;
            endVal = temp;
        }

        if (input && startInput && endInput && form) {
            input.value = 'kustom';
            startInput.value = startVal;
            endInput.value = endVal;
            if (msg) msg.classList.add('hidden');
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
