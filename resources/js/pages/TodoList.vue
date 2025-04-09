<script setup>
import { computed, inject, onMounted, ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { VueDraggableNext } from 'vue-draggable-next';

const router = useRouter();
const isAuthenticated = inject('isAuthenticated');
const checkAuth = inject('checkAuth');

const tasks = ref([]);
const newTask = ref({ title: '', description: '' });
const editingTask = ref(null);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const taskToDelete = ref(null);

const searchQuery = ref('');

const filteredTasks = computed(() => {
    return tasks.value.filter(task =>
        task.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        task.description.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const fetchTasks = async () => {
    try {
        const response = await axios.get('/api/tasks');
        tasks.value = response.data.sort((a, b) => a.order - b.order);
    } catch (error) {
        if (error.response.status === 401) {
            router.push('/login');
        }
    }
};

const addTask = async () => {
    if (newTask.value?.title.trim()) {
        try {
            await axios.post('/api/tasks', newTask.value);
            newTask.value = { title: '', description: '' };
            await fetchTasks();
        } catch (error) {
            console.error('Error addind taks:', error);
        }
    }
};

const deleteTask = async (id) => {
    try {
        await axios.delete(`/api/tasks/${id}`);
        await fetchTasks();
    } catch (error) {
        console.error('Error deleting task:', error);
    }
};

const openEditModal = (task) => {
    editingTask.value = { ...task };
    showEditModal.value = true;
};

const updateTask = async () => {
    try {
        await axios.put(`/api/tasks/${editingTask.value.id}`, editingTask.value);
        showEditModal.value = false;
        await fetchTasks();
    } catch (error) {
        console.error('Error updating task:', error);
    }
}

const closeModal = () => {
    showEditModal.value = false;
};

const openDeleteModal = (task) => {
    taskToDelete.value = task;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    taskToDelete.value = null;
};

const confirmDelete = async () => {
    if (taskToDelete.value) {
        try {
            await axios.delete(`/api/tasks/${taskToDelete.value.id}`);
            await fetchTasks();
            closeDeleteModal();
        } catch (error) {
            console.error('Error deleting task:', error);
        }
    }
};

const onDragEnd = async () => {
    // Mettre à jour l'ordre des tâches sur le serveur
    try {
        await axios.post('/api/tasks/reorder', { tasks: tasks.value });
    } catch (error) {
        console.error('Error reordering tasks:', error);
    }
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
    <div class="container mt-5">
        <h1 class="mb-4">Ma Liste de Tâches</h1>

        <!-- Ajout du champ de recherche -->
        <div class="input-group mb-3">
            <input v-model="searchQuery" type="text" class="form-control" placeholder="Rechercher une tâche...">
        </div>

        <div class="input-group mb-3">
            <input v-model="newTask.title" @keyup.enter="addTask" type="text" class="form-control" placeholder="Titre">
            <input v-model="newTask.description" @keyup.enter="addTask" type="text" class="form-control"
                placeholder="Description">
            <button @click="addTask" class="btn btn-outline-primary">Ajouter</button>
        </div>
        <ul class="list-group">
            <VueDraggableNext v-model="tasks" item-key="id" class="list-group" ghost-class="ghost" @end="onDragEnd"
                handle=".drag-handle">
                <li v-for="task in filteredTasks" :key="task.id"
                    class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="drag-handle me-2">
                            <i class="mdi mdi-drag-vertical"></i>
                        </span>
                        <div>
                            <strong>{{ task.title }}</strong>
                            <p>{{ task.description }}</p>
                        </div>
                    </div>
                    <div>
                        <button @click="openEditModal(task)" class="btn btn-primary btn-sm me-2"><i
                                class="mdi mdi-pencil"></i></button>
                        <button @click="openDeleteModal(task)" class="btn btn-danger btn-sm"><i
                                class="mdi mdi-delete"></i></button>
                    </div>
                </li>
            </VueDraggableNext>
        </ul>

        <!-- Modal d'édition -->
        <div v-if="showEditModal" class="modal d-block" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content" v-click-outside="closeModal">
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier une tâche</h5>
                        <button type="button" class="btn-close" @click="closeModal" data-dismiss="modal"
                            aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editTitle">Titre</label>
                            <input v-model="editingTask.title" type="text" id="editTitle" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="editDescription">Description</label>
                            <input v-model="editingTask.description" type="text" id="editDescription"
                                class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal"
                            data-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-primary" @click="updateTask">Modifier</button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="showEditModal" class="modal-backdrop fade show"></div>

        <!-- Modal de confirmation de suppression -->
        <div v-if="showDeleteModal" class="modal d-block" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmer la suppression</h5>
                        <button type="button" class="btn-close" @click="closeDeleteModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer cette tâche ?</p>
                        <p><strong>{{ taskToDelete?.title }}</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeDeleteModal">Annuler</button>
                        <button type="button" class="btn btn-danger" @click="confirmDelete">Supprimer</button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="showDeleteModal" class="modal-backdrop fade show"></div>
    </div>
</template>

<style scoped>
.modal-backdrop {
    opacity: 0.5;
}

.modal-header {
    cursor: move;
    cursor: grab;

    &:active {
        cursor: grabbing;
    }
}

.mdi {
    font-size: 18px;
}

.ghost {
    opacity: 0.5;
    background: #c8ebfb;
}

.drag-handle {
    cursor: move;
    color: #888;
    padding: 0 5px;
}

.drag-handle:hover {
    color: #333;
}

.list-group-item {
    cursor: default;
}
</style>
