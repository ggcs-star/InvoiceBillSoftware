@props(['type'])

<svg viewBox="0 0 16 16" class="h-4 w-4 shrink-0 {{ $type->colorClass() }}" role="img">
    <title>{{ $type->label() }}</title>
    <rect x="1" y="1" width="14" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="1.3"/>
    @if ($type->isTriangle())
        <path d="M8 4 L12.2 11.3 L3.8 11.3 Z" fill="currentColor"/>
    @else
        <circle cx="8" cy="8" r="3.4" fill="currentColor"/>
    @endif
</svg>
