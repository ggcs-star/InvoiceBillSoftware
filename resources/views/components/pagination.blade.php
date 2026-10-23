@props(['paginator'])

@if ($paginator->total() > 0)
    <div class="flex items-center justify-between text-sm text-ink-500 mt-4">
        <span>Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results</span>

        @php
            $current = $paginator->currentPage();
            $last = $paginator->lastPage();
            $start = max(1, $current - 2);
            $end = min($last, $current + 2);
        @endphp

        <div class="flex items-center gap-1">
            <a href="{{ $paginator->previousPageUrl() ?? '#' }}"
               class="flex h-8 w-8 items-center justify-center rounded-lg border border-ink-200 {{ $paginator->onFirstPage() ? 'pointer-events-none opacity-40' : 'text-ink-500 hover:bg-ink-50' }}">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            </a>

            @if ($start > 1)
                <a href="{{ $paginator->url(1) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-sm font-medium text-ink-600 hover:bg-ink-50">1</a>
                @if ($start > 2)
                    <span class="px-1 text-ink-400">&hellip;</span>
                @endif
            @endif

            @for ($page = $start; $page <= $end; $page++)
                <a href="{{ $paginator->url($page) }}"
                   class="flex h-8 w-8 items-center justify-center rounded-lg text-sm font-medium {{ $page === $current ? 'bg-brand-600 text-white' : 'text-ink-600 hover:bg-ink-50' }}">
                    {{ $page }}
                </a>
            @endfor

            @if ($end < $last)
                @if ($end < $last - 1)
                    <span class="px-1 text-ink-400">&hellip;</span>
                @endif
                <a href="{{ $paginator->url($last) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-sm font-medium text-ink-600 hover:bg-ink-50">{{ $last }}</a>
            @endif

            <a href="{{ $paginator->nextPageUrl() ?? '#' }}"
               class="flex h-8 w-8 items-center justify-center rounded-lg border border-ink-200 {{ ! $paginator->hasMorePages() ? 'pointer-events-none opacity-40' : 'text-ink-500 hover:bg-ink-50' }}">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
            </a>
        </div>
    </div>
@endif
