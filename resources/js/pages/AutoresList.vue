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

                <div v-else-if="autores.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <p class="mt-4 text-gray-500">Nenhum autor cadastrado</p>
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
                        <tr v-for="autor in autores" :key="autor.id" class="hover:bg-gray-50 transition-colors">
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
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { autorService } from '../services/autorService.js';

const autores = ref([]);
const loading = ref(false);
const error = ref(null);

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

const deleteAutor = async (id) => {
    if (!confirm('Tem certeza que deseja excluir este autor?')) {
        return;
    }
    try {
        await autorService.delete(id);
        await loadAutores();
    } catch (err) {
        error.value = err.response?.data?.message || 'Erro ao excluir autor';
    }
};

onMounted(() => {
    loadAutores();
});
</script>
