<script setup>
import { inject } from 'vue';

const isAuthenticated = inject('isAuthenticated');
</script>

<template>
    <div>
        <h1>Bienvenue sur l'application Todo</h1>
        <p v-if="isAuthenticated">Vous êtes connecté. Accédez à vos <router-link to="/tasks">tâches</router-link>.</p>
        <p v-else>Veuillez vous <router-link to="/login">connecter</router-link> pour gérer vos tâches.</p>
    </div>
</template>
