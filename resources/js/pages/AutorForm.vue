<template>
    <div>
        <div class="mb-6">
            <router-link
                to="/autores"
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
                    {{ isEdit ? 'Editar Autor' : 'Novo Autor' }}
                </h2>
            </div>

            <div class="px-6 py-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label for="nome" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nome <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="nome"
                            v-model="form.nome"
                            type="text"
                            required
                            placeholder="Digite o nome do autor"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none"
                        />
                        <p v-if="errors.nome" class="mt-1 text-sm text-red-600">{{ errors.nome }}</p>
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
                            to="/autores"
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
import { autorService } from '../services/autorService.js';

const router = useRouter();
const route = useRoute();

const isEdit = computed(() => !!route.params.id);

const form = ref({
    nome: '',
});

const loading = ref(false);
const error = ref(null);
const errors = ref({});

const loadAutor = async () => {
    if (!isEdit.value) return;
    
    loading.value = true;
    try {
        const response = await autorService.get(route.params.id);
        const autor = response.data;
        form.value = {
            nome: autor.nome,
        };
    } catch (err) {
        error.value = err.response?.data?.message || 'Erro ao carregar autor';
    } finally {
        loading.value = false;
    }
};

const submit = async () => {
    loading.value = true;
    error.value = null;
    errors.value = {};

    try {
        if (isEdit.value) {
            await autorService.update(route.params.id, form.value);
        } else {
            await autorService.create(form.value);
        }
        router.push('/autores');
    } catch (err) {
        if (err.response?.status === 422) {
            const message = err.response.data.message;
            if (typeof message === 'string') {
                error.value = message;
            } else {
                errors.value = message;
            }
        } else {
            error.value = err.response?.data?.message || 'Erro ao salvar autor';
        }
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadAutor();
});
</script>
