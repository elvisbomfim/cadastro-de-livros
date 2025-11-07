<template>
    <div>
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-3xl font-bold text-gray-900">Livros</h2>
            <router-link
                to="/livros/novo"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-md hover:shadow-lg"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Novo Livro
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
                        placeholder="Buscar livros por título ou editora..."
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none"
                    />
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

                <div v-else-if="livros.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <p class="mt-4 text-gray-500">Nenhum livro encontrado</p>
                </div>

                <table v-else class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Título
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Editora
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Edição
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Ano
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Preço
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Autores
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Assuntos
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="livro in livros" :key="livro.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ livro.titulo }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ livro.editora }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ livro.edicao }}ª</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ livro.ano_publicacao }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ livro.preco }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="autor in livro.autores"
                                        :key="autor.id"
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                    >
                                        {{ autor.nome }}
                                    </span>
                                    <span v-if="livro.autores.length === 0" class="text-xs text-gray-400">-</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="assunto in livro.assuntos"
                                        :key="assunto.id"
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                    >
                                        {{ assunto.descricao }}
                                    </span>
                                    <span v-if="livro.assuntos.length === 0" class="text-xs text-gray-400">-</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button
                                    @click="downloadFichaLivro(livro.id)"
                                    class="text-green-600 hover:text-green-900 disabled:text-green-400 disabled:cursor-not-allowed mr-4 transition-colors inline-flex items-center"
                                    :disabled="downloadingFicha[livro.id]"
                                    :title="downloadingFicha[livro.id] ? 'Gerando PDF...' : 'Baixar Ficha Detalhada'"
                                >
                                    <svg v-if="downloadingFicha[livro.id]" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </button>
                                <router-link
                                    :to="`/livros/${livro.id}/editar`"
                                    class="text-blue-600 hover:text-blue-900 mr-4 transition-colors"
                                >
                                    Editar
                                </router-link>
                                <button
                                    @click="deleteLivro(livro.id)"
                                    class="text-red-600 hover:text-red-900 transition-colors"
                                >
                                    Excluir
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div v-if="pagination && pagination.total > 0" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Mostrando <span class="font-semibold">{{ pagination.from }}</span> até
                        <span class="font-semibold">{{ pagination.to }}</span> de
                        <span class="font-semibold">{{ pagination.total }}</span> resultados
                    </div>
                    <div class="flex space-x-2">
                        <button
                            v-if="pagination.current_page > 1"
                            @click="changePage(pagination.current_page - 1)"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Anterior
                        </button>
                        <span class="px-4 py-2 text-sm text-gray-700">
                            Página {{ pagination.current_page }} de {{ pagination.last_page }}
                        </span>
                        <button
                            v-if="pagination.current_page < pagination.last_page"
                            @click="changePage(pagination.current_page + 1)"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Próxima
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { livroService } from '../services/livroService.js';
import { relatorioService } from '../services/relatorioService.js';

const route = useRoute();
const router = useRouter();

const livros = ref([]);
const loading = ref(false);
const error = ref(null);
const filter = ref('');
const pagination = ref(null);
const downloadingFicha = ref({});
const notification = ref({ show: false, message: '', type: 'info' });
let searchTimeout = null;

const loadLivros = async (page = 1) => {
    loading.value = true;
    error.value = null;
    try {
        const params = {
            page,
            per_page: 15,
        };
        if (filter.value) {
            params.filter = filter.value;
        }
        const response = await livroService.list(params);
        livros.value = response.data;
        pagination.value = response.meta;
    } catch (err) {
        error.value = err.response?.data?.message || 'Erro ao carregar livros';
    } finally {
        loading.value = false;
    }
};

const debounceSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadLivros(1);
    }, 500);
};

const deleteLivro = async (id) => {
    if (!confirm('Tem certeza que deseja excluir este livro?')) {
        return;
    }
    try {
        await livroService.delete(id);
        await loadLivros(pagination.value?.current_page || 1);
        showNotification('Livro excluído com sucesso!', 'success');
    } catch (err) {
        error.value = err.response?.data?.message || 'Erro ao excluir livro';
        showNotification('Erro ao excluir livro', 'error');
    }
};

const changePage = (page) => {
    loadLivros(page);
};

const showNotification = (message, type = 'info') => {
    notification.value = { show: true, message, type };
    setTimeout(() => {
        notification.value.show = false;
    }, 4000);
};

const downloadFichaLivro = async (id) => {
    try {
        downloadingFicha.value[id] = true;
        showNotification('Gerando ficha detalhada, aguarde...', 'info');
        
        const response = await relatorioService.downloadFichaLivroPdf(id);
        const blob = new Blob([response.data], { type: 'application/pdf' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `ficha-livro-${id}.pdf`;
        link.click();
        window.URL.revokeObjectURL(url);
        
        showNotification('Ficha detalhada gerada com sucesso!', 'success');
    } catch (err) {
        showNotification('Erro ao gerar ficha: ' + (err.response?.data?.message || err.message), 'error');
    } finally {
        downloadingFicha.value[id] = false;
    }
};

onMounted(() => {
    loadLivros();
    
    // Verifica se há mensagem de sucesso nos query params
    if (route.query.success) {
        showNotification(route.query.success, 'success');
        // Remove o query param da URL
        router.replace({ path: route.path, query: {} });
    }
});
</script>
