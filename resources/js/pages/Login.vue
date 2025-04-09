<script setup>
import { ref, inject } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const checkAuth = inject('checkAuth');

const email = ref('');
const password = ref('');
const error = ref('');

const login = async () => {
    try {
        await axios.post('/api/login', {
            email: email.value,
            password: password.value
        });
        await checkAuth();
        router.push('/tasks');
    } catch (err) {
        error.value = 'Identifiants incorrects';
    }
};
</script>

<template>
    <div class="col-md-6 offset-md-3">
        <h2 class="mb-4">Connexion</h2>
        <form @submit.prevent="login">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input v-model="email" type="email" class="form-control" id="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input v-model="password" type="password" class="form-control" id="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>
        <p v-if="error" class="text-danger mt-3">{{ error }}</p>
    </div>
</template>
