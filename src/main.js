import { createApp } from 'vue';
import App from './App.vue';
import './assets/css/main.css';
import i18n from './i18n';
import router from './router';


createApp(App).use(router).use(i18n).mount('#app')
