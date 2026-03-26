import './bootstrap';
import { createApp, nextTick } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './Routes/index';
import { useAuthStore } from './stores/authStore';

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);

const authStore = useAuthStore();

async function initApp() {
    // If we have a token but no user, fetch user from API first
    // This ensures Authorization header is properly set before any data loads
    if (authStore.token && !authStore.user) {
        await authStore.fetchUser();
    }
    authStore.initialized = true;
    
    // Only mount after auth is resolved
    app.mount('#app');
}

initApp();

