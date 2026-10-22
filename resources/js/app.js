import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

const tokenMeta = document.querySelector('meta[name="api-token"]');
if (tokenMeta && tokenMeta.content) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${tokenMeta.content}`;
}
