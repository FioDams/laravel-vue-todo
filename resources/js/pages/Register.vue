<script setup>
import { ref, inject } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const checkAuth = inject('checkAuth');

const name = ref('');
const email = ref('');
const password = ref('');
const password_confirmation = ref('');
const error = ref('');

const register = async () => {
    try {
        await axios.post('/api/register', {
            name: name.value,
            email: email.value,
            password: password.value,
            password_confirmation: password_confirmation.value
        });
        await checkAuth();
        router.push('/tasks');
    } catch (err) {
        if (err.response && err.response.data && err.response.data.errors) {
            error.value = Object.values(err.response.data.errors).flat().join(' ');
        } else {
            error.value = 'Une erreur est survenue lors de l\'enregistrement.';
        }
    }
};
</script>

<template>
    <div class="col-md-6 offset-md-3">
        <h2 class="mb-4">Créer un compte</h2>
        <form @submit.prevent="register">
            <div class="mb-3">
                <label for="name" class="form-label">Nom</label>
                <input v-model="name" type="text" class="form-control" id="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input v-model="email" type="email" class="form-control" id="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input v-model="password" type="password" class="form-control" id="password" required>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                <input v-model="password_confirmation" type="password" class="form-control" id="password_confirmation"
                    required>
            </div>
            <button type="submit" class="btn btn-primary">S'inscrire</button>
        </form>
        <p v-if="error" class="text-danger mt-3">{{ error }}</p>
        <p class="mt-3">Déjà un compte ? <router-link to="/login">Se connecter</router-link></p>
    </div>
</template>
