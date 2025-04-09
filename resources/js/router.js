import { createRouter, createWebHistory } from 'vue-router';
import Home from './pages/Home.vue';
import Login from './pages/Login.vue';
import Register from './pages/Register.vue';
import TodoList from './pages/TodoList.vue';

const routes = [
    { path: '/', component: Home },
    { path: '/login', component: Login },
    { path: '/register', component: Register },
    { path: '/tasks', component: TodoList },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
