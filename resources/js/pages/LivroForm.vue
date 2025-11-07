<template>
    <div>
        <div class="mb-6">
            <router-link
                to="/livros"
                class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-900 transition-colors"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Voltar para lista
            </router-link>
        </div>

        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <h2 class="text-2xl font-bold text-white">
                    {{ isEdit ? 'Editar Livro' : 'Novo Livro' }}
                </h2>
            </div>

            <div v-if="initialLoading" class="px-6 py-12 text-center">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                <p class="mt-4 text-gray-600">Carregando dados...</p>
            </div>

            <div v-else class="px-6 py-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Título -->
                    <div>
                        <label for="titulo" class="block text-sm font-semibold text-gray-700 mb-2">
                            Título <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="titulo"
                            v-model="form.titulo"
                            type="text"
                            required
                            placeholder="Digite o título do livro"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none"
                        />
                        <p v-if="errors.titulo" class="mt-1 text-sm text-red-600">{{ errors.titulo }}</p>
                    </div>

                    <!-- Editora -->
                    <div>
                        <label for="editora" class="block text-sm font-semibold text-gray-700 mb-2">
                            Editora <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="editora"
                            v-model="form.editora"
                            type="text"
                            required
                            placeholder="Digite o nome da editora"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none"
                        />
                        <p v-if="errors.editora" class="mt-1 text-sm text-red-600">{{ errors.editora }}</p>
                    </div>

                    <!-- Edição e Ano -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="edicao" class="block text-sm font-semibold text-gray-700 mb-2">
                                Edição <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="edicao"
                                v-model.number="form.edicao"
                                type="number"
                                min="1"
                                required
                                placeholder="Ex: 1"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none"
                            />
                            <p v-if="errors.edicao" class="mt-1 text-sm text-red-600">{{ errors.edicao }}</p>
                        </div>

                        <div>
                            <label for="ano_publicacao" class="block text-sm font-semibold text-gray-700 mb-2">
                                Ano de Publicação <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="ano_publicacao"
                                v-model.number="form.ano_publicacao"
                                type="number"
                                min="1900"
                                :max="new Date().getFullYear()"
                                required
                                placeholder="Ex: 2024"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none"
                            />
                            <p v-if="errors.ano_publicacao" class="mt-1 text-sm text-red-600">{{ errors.ano_publicacao }}</p>
                        </div>
                    </div>

                    <!-- Preço -->
                    <div>
                        <label for="preco" class="block text-sm font-semibold text-gray-700 mb-2">
                            Preço <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-gray-500 font-medium">R$</span>
                            <input
                                id="preco"
                                v-model.number="form.preco"
                                type="number"
                                step="0.01"
                                min="0"
                                required
                                placeholder="0.00"
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none"
                            />
                        </div>
                        <p v-if="errors.preco" class="mt-1 text-sm text-red-600">{{ errors.preco }}</p>
                    </div>

                    <!-- Autores -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Autores
                        </label>
                        <div class="border border-gray-300 rounded-lg p-3 min-h-[120px] max-h-[200px] overflow-y-auto bg-gray-50">
                            <div v-if="loadingAutores" class="text-center py-4 text-gray-500">
                                Carregando autores...
                            </div>
                            <div v-else-if="autores.length === 0" class="text-center py-4 text-gray-500">
                                Nenhum autor cadastrado
                            </div>
                            <div v-else class="space-y-2">
                                <label
                                    v-for="autor in autores"
                                    :key="autor.id"
                                    class="flex items-center p-2 hover:bg-blue-50 rounded cursor-pointer transition-colors"
                                >
                                    <input
                                        type="checkbox"
                                        :value="autor.id"
                                        v-model="form.autores"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    />
                                    <span class="ml-3 text-sm text-gray-700">{{ autor.nome }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Assuntos -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Assuntos
                        </label>
                        <div class="border border-gray-300 rounded-lg p-3 min-h-[120px] max-h-[200px] overflow-y-auto bg-gray-50">
                            <div v-if="loadingAssuntos" class="text-center py-4 text-gray-500">
                                Carregando assuntos...
                            </div>
                            <div v-else-if="assuntos.length === 0" class="text-center py-4 text-gray-500">
                                Nenhum assunto cadastrado
                            </div>
                            <div v-else class="space-y-2">
                                <label
                                    v-for="assunto in assuntos"
                                    :key="assunto.id"
                                    class="flex items-center p-2 hover:bg-blue-50 rounded cursor-pointer transition-colors"
                                >
                                    <input
                                        type="checkbox"
                                        :value="assunto.id"
                                        v-model="form.assuntos"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    />
                                    <span class="ml-3 text-sm text-gray-700">{{ assunto.descricao }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Mensagem de erro -->
                    <div v-if="error" class="bg-red-50 border-l-4 border-red-500 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-800">{{ error }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                        <router-link
                            to="/livros"
                            class="px-6 py-3 border-2 border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Cancelar
                        </router-link>
                        <button
                            type="submit"
                            :disabled="loading"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg text-sm font-semibold hover:from-blue-700 hover:to-blue-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-md hover:shadow-lg"
                        >
                            {{ loading ? 'Salvando...' : 'Salvar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { livroService } from '../services/livroService.js';
import { autorService } from '../services/autorService.js';
import { assuntoService } from '../services/assuntoService.js';

const router = useRouter();
const route = useRoute();

const isEdit = computed(() => !!route.params.id);

const form = ref({
    titulo: '',
    editora: '',
    edicao: 1,
    ano_publicacao: new Date().getFullYear(),
    preco: 0,
    autores: [],
    assuntos: [],
});

const loading = ref(false);
const initialLoading = ref(true);
const loadingAutores = ref(false);
const loadingAssuntos = ref(false);
const error = ref(null);
const errors = ref({});
const autores = ref([]);
const assuntos = ref([]);

let autoresLoaded = false;
let assuntosLoaded = false;

const checkInitialLoading = () => {
    if (!isEdit.value && autoresLoaded && assuntosLoaded) {
        initialLoading.value = false;
    }
};

const loadAutores = async () => {
    loadingAutores.value = true;
    try {
        const response = await autorService.list();
        autores.value = response.data;
        autoresLoaded = true;
    } catch (err) {
        console.error('Erro ao carregar autores:', err);
        autoresLoaded = true; // Marcar como carregado mesmo em caso de erro
    } finally {
        loadingAutores.value = false;
        checkInitialLoading();
    }
};

const loadAssuntos = async () => {
    loadingAssuntos.value = true;
    try {
        const response = await assuntoService.list();
        assuntos.value = response.data;
        assuntosLoaded = true;
    } catch (err) {
        console.error('Erro ao carregar assuntos:', err);
        assuntosLoaded = true; // Marcar como carregado mesmo em caso de erro
    } finally {
        loadingAssuntos.value = false;
        checkInitialLoading();
    }
};

const loadLivro = async () => {
    if (!isEdit.value) {
        // Em modo de criação, espera autores e assuntos serem carregados
        return;
    }
    
    loading.value = true;
    try {
        const response = await livroService.get(route.params.id);
        const livro = response.data;
        form.value = {
            titulo: livro.titulo,
            editora: livro.editora,
            edicao: livro.edicao,
            ano_publicacao: livro.ano_publicacao,
            preco: livro.preco_value ?? livro.preco,
            autores: livro.autores ? livro.autores.map(a => a.id) : [],
            assuntos: livro.assuntos ? livro.assuntos.map(a => a.id) : [],
        };
    } catch (err) {
        error.value = err.response?.data?.message || 'Erro ao carregar livro';
    } finally {
        loading.value = false;
        // Em modo de edição, desativa loading quando livro for carregado
        if (autoresLoaded && assuntosLoaded) {
            initialLoading.value = false;
        }
    }
};

const submit = async () => {
    loading.value = true;
    error.value = null;
    errors.value = {};

    try {
        if (isEdit.value) {
            await livroService.update(route.params.id, form.value);
        } else {
            await livroService.create(form.value);
        }
        router.push('/livros');
    } catch (err) {
        if (err.response?.status === 422) {
            const message = err.response.data.message;
            if (typeof message === 'string') {
                error.value = message;
            } else {
                errors.value = message;
            }
        } else {
            error.value = err.response?.data?.message || 'Erro ao salvar livro';
        }
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadAutores();
    loadAssuntos();
    loadLivro();
});
</script>
