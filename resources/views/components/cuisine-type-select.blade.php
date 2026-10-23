@props(['name', 'options', 'selected' => ''])

<div x-data="cuisineTypeSelect({
        options: {{ \Illuminate\Support\Js::from($options) }},
        selected: {{ \Illuminate\Support\Js::from((string) $selected) }},
        storeUrl: {{ \Illuminate\Support\Js::from(route('admin.cuisine-types.store')) }},
        csrfToken: {{ \Illuminate\Support\Js::from(csrf_token()) }}
    })"
    @click.outside="open = false"
    class="relative">
    <input type="hidden" name="{{ $name }}" x-model="selectedValue">

    <button type="button" @click="toggle()"
            class="w-full flex items-center justify-between gap-2 rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm text-left shadow-sm">
        <span x-text="selectedLabel" class="truncate" :class="selectedValue ? 'text-ink-900' : 'text-ink-500'"></span>
        <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0 text-ink-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 9l6 6 6-6"/>
        </svg>
    </button>

    <div x-show="open" x-cloak x-transition class="absolute z-20 mt-1 w-full min-w-[260px] rounded-lg border border-ink-100 bg-white shadow-lg">
        <template x-if="!adding">
            <div>
                <div class="p-2 border-b border-ink-100">
                    <div class="relative">
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-ink-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="7"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" x-ref="search" x-model="query" placeholder="Search cuisine..."
                               class="w-full rounded-md border-ink-200 pl-8 pr-2 py-1.5 text-sm">
                    </div>
                </div>
                <ul class="max-h-56 overflow-y-auto py-1">
                    <template x-for="opt in filteredOptions" :key="opt.value">
                        <li @click="select(opt.value, opt.label)" x-text="opt.label"
                            class="px-3 py-1.5 text-sm cursor-pointer hover:bg-brand-50"
                            :class="opt.value === selectedValue ? 'bg-brand-50 text-brand-600 font-medium' : 'text-ink-700'">
                        </li>
                    </template>
                    <li x-show="filteredOptions.length === 0" class="px-3 py-1.5 text-sm text-ink-400">No matches</li>
                </ul>
                <button type="button" @click="startAdding()"
                        class="flex w-full items-center gap-1.5 border-t border-ink-100 px-3 py-2 text-sm font-medium text-brand-600 hover:bg-brand-50">
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Cuisine
                </button>
            </div>
        </template>

        <template x-if="adding">
            <div class="p-3 space-y-2">
                <label class="block text-xs font-medium text-ink-500">New cuisine type</label>
                <input type="text" x-ref="newNameInput" x-model="newName" @keydown.enter.prevent="saveNew()"
                       placeholder="e.g. Thai"
                       class="w-full rounded-md border-ink-200 text-sm px-2 py-1.5">
                <p x-show="error" x-text="error" class="text-xs text-red-600"></p>
                <div class="flex gap-2">
                    <button type="button" @click="saveNew()" :disabled="saving"
                            class="rounded-md bg-brand-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-700 disabled:opacity-50">
                        <span x-show="!saving">Save</span>
                        <span x-show="saving">Saving&hellip;</span>
                    </button>
                    <button type="button" @click="adding = false"
                            class="rounded-md border border-ink-200 px-3 py-1.5 text-xs font-medium text-ink-600 hover:bg-ink-50">
                        Cancel
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>
