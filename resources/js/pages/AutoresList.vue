<template>
    <div>
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-3xl font-bold text-gray-900">Autores</h2>
            <router-link
                to="/autores/novo"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-md hover:shadow-lg"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Novo Autor
            </router-link>
        </div>

        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        v-model="filter"
                        @input="debounceSearch"
                        type="text"
                        placeholder="Buscar autores por nome..."
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none"
                    />
                </div>
            </div>

            <div class="overflow-x-auto">
                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-4 text-gray-500">Carregando...</p>
                </div>

                <div v-else-if="error" class="text-center py-12">
                    <div class="bg-red-50 border-l-4 border-red-500 rounded-md p-4 mx-6">
                        <p class="text-red-800">{{ error }}</p>
                    </div>
                </div>

                <div v-else-if="filteredAutores.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <p class="mt-4 text-gray-500">{{ filter ? 'Nenhum autor encontrado' : 'Nenhum autor cadastrado' }}</p>
                </div>

                <table v-else class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Nome
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="autor in filteredAutores" :key="autor.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ autor.nome }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <router-link
                                    :to="`/autores/${autor.id}/editar`"
                                    class="text-blue-600 hover:text-blue-900 mr-4 transition-colors"
                                >
                                    Editar
                                </router-link>
                                <button
                                    @click="deleteAutor(autor.id)"
                                    class="text-red-600 hover:text-red-900 transition-colors"
                                >
                                    Excluir
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Notificação -->
        <transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-2"
        >
            <div
                v-if="notification.show"
                :class="{
                    'bg-blue-50 border-blue-200 text-blue-800': notification.type === 'info',
                    'bg-green-50 border-green-200 text-green-800': notification.type === 'success',
                    'bg-red-50 border-red-200 text-red-800': notification.type === 'error'
                }"
                class="fixed top-4 right-4 border px-6 py-4 rounded-lg shadow-lg z-50 flex items-center gap-3 min-w-[300px]"
            >
                <svg v-if="notification.type === 'info'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg v-else-if="notification.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="flex-1">{{ notification.message }}</p>
                <button @click="notification.show = false" class="text-current opacity-70 hover:opacity-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { autorService } from '../services/autorService.js';

const route = useRoute();
const router = useRouter();

const autores = ref([]);
const loading = ref(true);
const error = ref(null);
const filter = ref('');
const notification = ref({ show: false, message: '', type: 'info' });
let searchTimeout = null;

const filteredAutores = computed(() => {
    if (!filter.value) {
        return autores.value;
    }
    const searchTerm = filter.value.toLowerCase();
    return autores.value.filter(autor => 
        autor.nome.toLowerCase().includes(searchTerm)
    );
});

const loadAutores = async () => {
    loading.value = true;
    error.value = null;
    try {
        const response = await autorService.list();
        autores.value = response.data;
    } catch (err) {
        error.value = err.response?.data?.message || 'Erro ao carregar autores';
    } finally {
        loading.value = false;
    }
};

const debounceSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        // A busca é feita via computed, não precisa fazer nada aqui
    }, 300);
};

const deleteAutor = async (id) => {
    if (!confirm('Tem certeza que deseja excluir este autor?')) {
        return;
    }
    try {
        await autorService.delete(id);
        await loadAutores();
        showNotification('Autor excluído com sucesso!', 'success');
    } catch (err) {
        error.value = err.response?.data?.message || 'Erro ao excluir autor';
        showNotification('Erro ao excluir autor', 'error');
    }
};

const showNotification = (message, type = 'info') => {
    notification.value = { show: true, message, type };
    setTimeout(() => {
        notification.value.show = false;
    }, 4000);
};

onMounted(() => {
    loadAutores();
    
    // Verifica se há mensagem de sucesso nos query params
    if (route.query.success) {
        showNotification(route.query.success, 'success');
        // Remove o query param da URL
        router.replace({ path: route.path, query: {} });
    }
});
</script>
