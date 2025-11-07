<template>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Relatório de Livros por Categoria</h1>
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
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Gráfico de Barras</h2>
                <div class="h-96">
                    <canvas ref="barChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Gráfico de Pizza</h2>
                <div class="h-96">
                    <canvas ref="pieChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total de Livros</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Total</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Preço Médio</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="item in dados" :key="item.categoria">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.categoria || 'Sem categoria' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ item.total_livros || 0 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">R$ {{ formatCurrency(item.valor_total) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">R$ {{ formatCurrency(item.preco_medio) }}</td>
                        </tr>
                        <tr class="bg-gray-50 font-semibold">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TOTAL GERAL</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ totalLivros }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">R$ {{ formatCurrency(valorTotalGeral) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, nextTick } from 'vue';
import { relatorioService } from '../services/relatorioService';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const loading = ref(true);
const error = ref(null);
const dados = ref([]);
const barChart = ref(null);
const pieChart = ref(null);
const downloadingPdf = ref(false);
const notification = ref({ show: false, message: '', type: 'info' });
let barChartInstance = null;
let pieChartInstance = null;

const totalLivros = computed(() => {
    return dados.value.reduce((sum, item) => sum + (parseInt(item.total_livros) || 0), 0);
});

const valorTotalGeral = computed(() => {
    return dados.value.reduce((sum, item) => sum + (parseFloat(item.valor_total) || 0), 0);
});

const formatCurrency = (value) => {
    return (parseFloat(value) || 0).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

const loadData = async () => {
    try {
        loading.value = true;
        error.value = null;
        const response = await relatorioService.livrosPorCategoria();
        console.log('Response data:', response.data);
        dados.value = response.data.data || response.data || [];
        console.log('Dados processados:', dados.value);
        
        await nextTick();
        setTimeout(() => {
            createCharts();
        }, 200);
    } catch (err) {
        error.value = 'Erro ao carregar dados: ' + (err.response?.data?.message || err.message);
        console.error('Erro ao carregar dados:', err);
    } finally {
        loading.value = false;
    }
};

const createCharts = () => {
    console.log('Criando gráficos...', {
        barChart: !!barChart.value,
        pieChart: !!pieChart.value,
        dadosLength: dados.value.length,
        dados: dados.value
    });
    
    if (!barChart.value || !pieChart.value) {
        console.warn('Elementos canvas não encontrados');
        return;
    }
    
    if (dados.value.length === 0) {
        console.warn('Nenhum dado disponível para os gráficos');
        return;
    }

    const labels = dados.value.map(item => item.categoria || 'Sem categoria');
    const livrosData = dados.value.map(item => parseInt(item.total_livros) || 0);
    
    console.log('Labels:', labels);
    console.log('Dados de livros:', livrosData);

    const colors = [
        'rgba(59, 130, 246, 0.8)',
        'rgba(16, 185, 129, 0.8)',
        'rgba(245, 158, 11, 0.8)',
        'rgba(239, 68, 68, 0.8)',
        'rgba(139, 92, 246, 0.8)',
        'rgba(236, 72, 153, 0.8)',
    ];

    if (barChartInstance) {
        barChartInstance.destroy();
    }

    barChartInstance = new Chart(barChart.value, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total de Livros',
                data: livrosData,
                backgroundColor: colors,
                borderColor: colors.map(c => c.replace('0.8', '1')),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    if (pieChartInstance) {
        pieChartInstance.destroy();
    }

    pieChartInstance = new Chart(pieChart.value, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: livrosData,
                backgroundColor: colors,
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
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
        
        const response = await relatorioService.downloadLivrosPorCategoriaPdf();
        const blob = new Blob([response.data], { type: 'application/pdf' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'livros-por-categoria.pdf';
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

