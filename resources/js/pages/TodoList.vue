<script setup>
import { inject, onMounted, ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const isAuthenticated = inject('isAuthenticated');
const checkAuth = inject('checkAuth');

const tasks = ref([]);
const newTask = ref({ title: '', description: '' });

const fetchTasks = async () => {
    try {
        const response = await axios.get('/api/tasks');
        tasks.value = response.data;
    } catch (error) {
        if (error.response.status === 401) {
            router.push('/login');
        }
    }
};

const addTask = async () => {
    if (newTask.value?.title.trim()) {
        await axios.post('/api/tasks', newTask.value);
        newTask.value = { title: '', description: '' };
        await fetchTasks();
    }
};

const deleteTask = async (id) => {
    await axios.delete(`/api/tasks/${id}`);
    await fetchTasks();
};

onMounted(async () => {
    await checkAuth();
    if (isAuthenticated.value) {
        await fetchTasks();
    } else {
        router.push('/login');
    }
});
</script>

<template>
    <div>
        <h1 class="mb-4">Ma Liste de Tâches</h1>
        <div class="input-group mb-3">
            <input v-model="newTask.title" @keyup.enter="addTask" type="text" class="form-control" placeholder="Titre">
            <input v-model="newTask.description" @keyup.enter="addTask" type="text" class="form-control"
                placeholder="Description">
            <button @click="addTask" class="btn btn-outline-primary">Ajouter</button>
        </div>
        <ul class="list-group">
            <li v-for="task in tasks" :key="task.id"
                class="list-group-item d-flex justify-content-between align-items-center">
                <b>{{ task.title }}</b>{{ task.description }}
                <button @click="deleteTask(task.id)" class="btn btn-danger btn-sm">Supprimer</button>
            </li>
        </ul>
    </div>
</template>
