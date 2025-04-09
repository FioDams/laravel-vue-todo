<script setup>
import { ref, provide } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const isAuthenticated = ref(false);

const checkAuth = async () => {
    try {
        await axios.get('/api/user');
        isAuthenticated.value = true;
    } catch (error) {
        isAuthenticated.value = false;
    }
};

const logout = async () => {
    try {
        await axios.post('/api/logout');
        isAuthenticated.value = false;
        router.push('/');
    } catch (error) {
        console.error('Erreur lors de la déconnexion', error);
    }
};

provide('isAuthenticated', isAuthenticated);
provide('checkAuth', checkAuth);

checkAuth();
</script>

<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <router-link class="navbar-brand" to="/">Laravel Vue Todo</router-link>
                <div class="navbar-nav">
                    <router-link class="nav-link" to="/">Accueil</router-link>
                    <template v-if="isAuthenticated">
                        <router-link class="nav-link" to="/tasks">Tâches</router-link>
                        <a class="nav-link" href="#" @click.prevent="logout">Déconnexion</a>
                    </template>
                    <template v-else>
                        <router-link class="nav-link" to="/login">Connexion</router-link>
                        <router-link class="nav-link" to="/register">Inscription</router-link>
                    </template>
                </div>
            </div>
        </nav>
        <div class="container mt-4">
            <router-view></router-view>
        </div>
    </div>
</template>
