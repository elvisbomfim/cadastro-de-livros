<template>
    <div>
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-3xl font-bold text-gray-900">Assuntos</h2>
            <router-link
                to="/assuntos/novo"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-md hover:shadow-lg"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Novo Assunto
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
                        placeholder="Buscar assuntos por descrição..."
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

                <div v-else-if="filteredAssuntos.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <p class="mt-4 text-gray-500">{{ filter ? 'Nenhum assunto encontrado' : 'Nenhum assunto cadastrado' }}</p>
                </div>

                <table v-else class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Descrição
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="assunto in filteredAssuntos" :key="assunto.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ assunto.descricao }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <router-link
                                    :to="`/assuntos/${assunto.id}/editar`"
                                    class="text-blue-600 hover:text-blue-900 mr-4 transition-colors"
                                >
                                    Editar
                                </router-link>
                                <button
                                    @click="deleteAssunto(assunto.id)"
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
import { ref, onMounted, computed } from 'vue';
import { assuntoService } from '../services/assuntoService.js';

const assuntos = ref([]);
const loading = ref(true);
const error = ref(null);
const filter = ref('');
let searchTimeout = null;

const filteredAssuntos = computed(() => {
    if (!filter.value) {
        return assuntos.value;
    }
    const searchTerm = filter.value.toLowerCase();
    return assuntos.value.filter(assunto => 
        assunto.descricao.toLowerCase().includes(searchTerm)
    );
});

const loadAssuntos = async () => {
    loading.value = true;
    error.value = null;
    try {
        const response = await assuntoService.list();
        assuntos.value = response.data;
    } catch (err) {
        error.value = err.response?.data?.message || 'Erro ao carregar assuntos';
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

const deleteAssunto = async (id) => {
    if (!confirm('Tem certeza que deseja excluir este assunto?')) {
        return;
    }
    try {
        await assuntoService.delete(id);
        await loadAssuntos();
    } catch (err) {
        error.value = err.response?.data?.message || 'Erro ao excluir assunto';
    }
};

onMounted(() => {
    loadAssuntos();
});
</script>
