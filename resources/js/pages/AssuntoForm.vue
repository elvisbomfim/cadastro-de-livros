<template>
    <div>
        <div class="mb-6">
            <router-link
                to="/assuntos"
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
                    {{ isEdit ? 'Editar Assunto' : 'Novo Assunto' }}
                </h2>
            </div>

            <div v-if="initialLoading" class="px-6 py-12 text-center">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                <p class="mt-4 text-gray-600">Carregando dados...</p>
            </div>

            <div v-else class="px-6 py-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label for="descricao" class="block text-sm font-semibold text-gray-700 mb-2">
                            Descrição <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="descricao"
                            v-model="form.descricao"
                            type="text"
                            required
                            placeholder="Digite a descrição do assunto"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none"
                        />
                        <p v-if="errors.descricao" class="mt-1 text-sm text-red-600">{{ errors.descricao }}</p>
                    </div>

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

                    <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                        <router-link
                            to="/assuntos"
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
import { assuntoService } from '../services/assuntoService.js';

const router = useRouter();
const route = useRoute();

const isEdit = computed(() => !!route.params.id);

const form = ref({
    descricao: '',
});

const loading = ref(false);
const initialLoading = ref(true);
const error = ref(null);
const errors = ref({});

const loadAssunto = async () => {
    if (!isEdit.value) {
        initialLoading.value = false;
        return;
    }
    
    loading.value = true;
    try {
        const response = await assuntoService.get(route.params.id);
        const assunto = response.data;
        form.value = {
            descricao: assunto.descricao,
        };
    } catch (err) {
        error.value = err.response?.data?.message || 'Erro ao carregar assunto';
    } finally {
        loading.value = false;
        initialLoading.value = false;
    }
};

const submit = async () => {
    loading.value = true;
    error.value = null;
    errors.value = {};

    try {
        if (isEdit.value) {
            await assuntoService.update(route.params.id, form.value);
            router.push({ path: '/assuntos', query: { success: 'Assunto atualizado com sucesso!' } });
        } else {
            await assuntoService.create(form.value);
            router.push({ path: '/assuntos', query: { success: 'Assunto criado com sucesso!' } });
        }
    } catch (err) {
        if (err.response?.status === 422) {
            const message = err.response.data.message;
            if (typeof message === 'string') {
                error.value = message;
            } else {
                errors.value = message;
            }
        } else {
            error.value = err.response?.data?.message || 'Erro ao salvar assunto';
        }
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadAssunto();
});
</script>
