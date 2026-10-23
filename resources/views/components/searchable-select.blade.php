@props(['name', 'options', 'selected' => '', 'placeholder' => 'All'])

<div x-data="searchableSelect({
        options: {{ \Illuminate\Support\Js::from($options) }},
        selected: {{ \Illuminate\Support\Js::from((string) $selected) }},
        placeholder: {{ \Illuminate\Support\Js::from($placeholder) }}
    })"
    @click.outside="open = false"
    class="relative">
    <input type="hidden" name="{{ $name }}" x-model="selectedValue">

    <button type="button" @click="toggle()"
            class="w-full min-w-[170px] flex items-center justify-between gap-2 rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm text-left shadow-sm">
        <span x-text="selectedLabel" class="truncate" :class="selectedValue ? 'text-ink-900' : 'text-ink-500'"></span>
        <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0 text-ink-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 9l6 6 6-6"/>
        </svg>
    </button>

    <div x-show="open" x-cloak x-transition
         class="absolute z-20 mt-1 w-full min-w-[220px] rounded-lg border border-ink-100 bg-white shadow-lg">
        <div class="p-2 border-b border-ink-100">
            <div class="relative">
                <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-ink-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="7"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" x-ref="search" x-model="query" placeholder="Search..."
                       class="w-full rounded-md border-ink-200 pl-8 pr-2 py-1.5 text-sm">
            </div>
        </div>
        <ul class="max-h-56 overflow-y-auto py-1">
            <li @click="select('', placeholder)"
                class="px-3 py-1.5 text-sm cursor-pointer hover:bg-brand-50 text-ink-500">
                <span x-text="placeholder"></span>
            </li>
            <template x-for="opt in filteredOptions" :key="opt.value">
                <li @click="select(opt.value, opt.label)" x-text="opt.label"
                    class="px-3 py-1.5 text-sm cursor-pointer hover:bg-brand-50"
                    :class="String(opt.value) === selectedValue ? 'bg-brand-50 text-brand-600 font-medium' : 'text-ink-700'">
                </li>
            </template>
            <li x-show="filteredOptions.length === 0" class="px-3 py-1.5 text-sm text-ink-400">No matches</li>
        </ul>
    </div>
</div>
