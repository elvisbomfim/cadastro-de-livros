<template>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Relatório por Autor</h1>
            <button
                @click="downloadPdf"
                class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-all"
                :disabled="loading || downloadingPdf"
            >
                <svg v-if="downloadingPdf" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ downloadingPdf ? 'Gerando PDF...' : 'Baixar PDF' }}
            </button>
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

        <div v-if="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <p class="mt-4 text-gray-600">Carregando dados...</p>
        </div>

        <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            {{ error }}
        </div>

        <div v-else>
            <div v-for="autor in dados" :key="autor.autor_id" class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="border-b border-gray-200 pb-4 mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ autor.autor_nome }}</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-sm text-gray-600 mb-1">Total de Livros</div>
                        <div class="text-2xl font-bold text-blue-600">{{ autor.total_livros || 0 }}</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="text-sm text-gray-600 mb-1">Valor Total</div>
                        <div class="text-2xl font-bold text-green-600">R$ {{ formatCurrency(autor.valor_total) }}</div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="text-sm text-gray-600 mb-1">Preço Médio</div>
                        <div class="text-2xl font-bold text-yellow-600">R$ {{ formatCurrency(autor.preco_medio) }}</div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <div class="text-sm text-gray-600 mb-1">Período</div>
                        <div class="text-lg font-bold text-purple-600">
                            <span v-if="autor.primeiro_livro_ano && autor.ultimo_livro_ano">
                                {{ autor.primeiro_livro_ano }} - {{ autor.ultimo_livro_ano }}
                            </span>
                            <span v-else>-</span>
                        </div>
                    </div>
                </div>

                <div v-if="autor.titulos_livros" class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Livros:</h3>
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="titulo in autor.titulos_livros.split(' | ')"
                            :key="titulo"
                            class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm"
                        >
                            {{ titulo.trim() }}
                        </span>
                    </div>
                </div>

                <div v-if="autor.categorias" class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Categorias:</h3>
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="categoria in autor.categorias.split(', ')"
                            :key="categoria"
                            class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm"
                        >
                            {{ categoria.trim() }}
                        </span>
                    </div>
                </div>
            </div>

            <div v-if="dados.length === 0" class="text-center py-12 text-gray-500">
                Nenhum dado encontrado.
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { relatorioService } from '../services/relatorioService';

const loading = ref(true);
const error = ref(null);
const dados = ref([]);
const downloadingPdf = ref(false);
const notification = ref({ show: false, message: '', type: 'info' });

const formatCurrency = (value) => {
    return (parseFloat(value) || 0).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

const loadData = async () => {
    try {
        loading.value = true;
        error.value = null;
        const response = await relatorioService.relatorioPorAutor();
        dados.value = response.data.data || [];
    } catch (err) {
        error.value = 'Erro ao carregar dados: ' + (err.response?.data?.message || err.message);
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const showNotification = (message, type = 'info') => {
    notification.value = { show: true, message, type };
    setTimeout(() => {
        notification.value.show = false;
    }, 4000);
};

const downloadPdf = async () => {
    try {
        downloadingPdf.value = true;
        showNotification('Gerando PDF, aguarde...', 'info');
        
        const response = await relatorioService.downloadRelatorioPorAutorPdf();
        const blob = new Blob([response.data], { type: 'application/pdf' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'relatorio-por-autor.pdf';
        link.click();
        window.URL.revokeObjectURL(url);
        
        showNotification('PDF gerado com sucesso!', 'success');
    } catch (err) {
        showNotification('Erro ao gerar PDF: ' + (err.response?.data?.message || err.message), 'error');
    } finally {
        downloadingPdf.value = false;
    }
};

onMounted(() => {
    loadData();
});
</script>

