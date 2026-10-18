@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-3">
        {{-- Results Info --}}
        <div class="text-xs text-slate-500 text-center sm:text-left order-2 sm:order-1">
            Menampilkan
            <span class="font-bold text-slate-900">{{ $paginator->firstItem() ?? 0 }}</span>
            &ndash;
            <span class="font-bold text-slate-900">{{ $paginator->lastItem() ?? 0 }}</span>
            dari
            <span class="font-bold text-slate-900">{{ $paginator->total() }}</span>
            data
        </div>

        {{-- Pagination Buttons --}}
        <div class="flex items-center justify-center sm:justify-end gap-1.5 select-none order-1 sm:order-2 flex-wrap">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="inline-flex items-center gap-1 sm:gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-300 bg-slate-50 border border-slate-100 cursor-not-allowed min-h-[38px]">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                    <span>Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1 sm:gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 border border-slate-200 shadow-2xs transition-all active:scale-95 min-h-[38px] cursor-pointer">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                    <span>Sebelumnya</span>
                </a>
            @endif

            {{-- Numeric Page Links --}}
            <div class="flex items-center gap-1">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span aria-disabled="true" class="w-7 sm:w-8 h-8 sm:h-9 flex items-center justify-center text-xs font-bold text-slate-400">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="w-8 sm:w-9 h-8 sm:h-9 rounded-xl bg-orange-500 text-white font-black text-xs flex items-center justify-center shadow-xs shadow-orange-500/25">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="w-8 sm:w-9 h-8 sm:h-9 rounded-xl bg-white hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 border border-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center transition-all shadow-2xs active:scale-95 cursor-pointer">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1 sm:gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 border border-slate-200 shadow-2xs transition-all active:scale-95 min-h-[38px] cursor-pointer">
                    <span>Selanjutnya</span>
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            @else
                <span aria-disabled="true" class="inline-flex items-center gap-1 sm:gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-300 bg-slate-50 border border-slate-100 cursor-not-allowed min-h-[38px]">
                    <span>Selanjutnya</span>
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
