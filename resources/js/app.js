import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('searchableSelect', ({ options, selected, placeholder }) => ({
    open: false,
    query: '',
    options,
    placeholder,
    selectedValue: selected ?? '',
    selectedLabel: (options.find((o) => String(o.value) === String(selected)) || {}).label ?? placeholder,

    get filteredOptions() {
        if (!this.query) {
            return this.options;
        }
        const needle = this.query.toLowerCase();
        return this.options.filter((o) => o.label.toLowerCase().includes(needle));
    },

    toggle() {
        this.open = !this.open;
        if (this.open) {
            this.$nextTick(() => this.$refs.search.focus());
        }
    },

    select(value, label) {
        this.selectedValue = String(value);
        this.selectedLabel = value ? label : this.placeholder;
        this.open = false;
        this.query = '';
        this.$dispatch('searchable-select-change', { value: this.selectedValue, label: this.selectedLabel });
    },
}));

Alpine.data('cuisineTypeSelect', ({ options, selected, storeUrl, csrfToken }) => ({
    open: false,
    adding: false,
    saving: false,
    query: '',
    newName: '',
    error: null,
    options,
    selectedValue: selected ?? '',
    selectedLabel: (options.find((o) => o.value === selected) || {}).label ?? 'Select cuisine type',

    get filteredOptions() {
        if (!this.query) {
            return this.options;
        }
        const needle = this.query.toLowerCase();
        return this.options.filter((o) => o.label.toLowerCase().includes(needle));
    },

    toggle() {
        this.open = !this.open;
        this.adding = false;
        if (this.open) {
            this.$nextTick(() => this.$refs.search?.focus());
        }
    },

    select(value, label) {
        this.selectedValue = value;
        this.selectedLabel = label;
        this.open = false;
        this.query = '';
    },

    startAdding() {
        this.adding = true;
        this.newName = this.query;
        this.error = null;
        this.$nextTick(() => this.$refs.newNameInput?.focus());
    },

    async saveNew() {
        const name = this.newName.trim();
        if (!name) {
            return;
        }

        this.saving = true;
        this.error = null;

        try {
            const response = await fetch(this.storeUrlValue, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfTokenValue,
                },
                body: JSON.stringify({ name }),
            });

            if (!response.ok) {
                throw new Error('Request failed');
            }

            const data = await response.json();

            if (!this.options.some((o) => o.value === data.name)) {
                this.options.push({ value: data.name, label: data.name });
            }

            this.select(data.name, data.name);
            this.adding = false;
            this.newName = '';
        } catch (e) {
            this.error = 'Could not add cuisine type. Try again.';
        } finally {
            this.saving = false;
        }
    },

    storeUrlValue: storeUrl,
    csrfTokenValue: csrfToken,
}));

Alpine.start();

const tokenMeta = document.querySelector('meta[name="api-token"]');
if (tokenMeta && tokenMeta.content) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${tokenMeta.content}`;
}
