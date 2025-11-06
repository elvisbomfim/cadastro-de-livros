import './bootstrap';
import { createApp } from 'vue';
import router from './router/index.js';
import Layout from './components/Layout.vue';

// Criar instância Vue apenas se o elemento #app existir
const appElement = document.getElementById('app');
if (appElement) {
    const app = createApp(Layout);
    
    // Usar o router
    app.use(router);
    
    // Montar a aplicação
    app.mount('#app');
}
