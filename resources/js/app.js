import './bootstrap';
import { createApp } from 'vue';
import App from './components/App.vue';
import router from './router';

import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import '@mdi/font/css/materialdesignicons.css'

const app = createApp(App);
app.use(router);
app.mount('#app');
