<template>
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <router-link to="/" class="text-xl font-bold text-gray-900">
                                Cadastro de Livros
                            </router-link>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <router-link
                                to="/livros"
                                class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors"
                                active-class="border-blue-500 text-gray-900"
                            >
                                Livros
                            </router-link>
                            <router-link
                                to="/autores"
                                class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors"
                                active-class="border-blue-500 text-gray-900"
                            >
                                Autores
                            </router-link>
                            <router-link
                                to="/assuntos"
                                class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors"
                                active-class="border-blue-500 text-gray-900"
                            >
                                Assuntos
                            </router-link>
                            <div class="relative flex" @mouseenter="showRelatoriosMenu = true" @mouseleave="showRelatoriosMenu = false">
                                <router-link
                                    to="/relatorios/livros-por-categoria"
                                    class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors"
                                    :class="{ 'border-blue-500 text-gray-900': isRelatorioActive }"
                                >
                                    Relatórios
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </router-link>
                                <transition
                                    enter-active-class="transition ease-out duration-200"
                                    enter-from-class="opacity-0 translate-y-1"
                                    enter-to-class="opacity-100 translate-y-0"
                                    leave-active-class="transition ease-in duration-150"
                                    leave-from-class="opacity-100 translate-y-0"
                                    leave-to-class="opacity-0 translate-y-1"
                                >
                                    <div 
                                        v-show="showRelatoriosMenu"
                                        class="absolute left-0 mt-2 w-56 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50"
                                        style="top: 100%;"
                                    >
                                        <div class="py-1">
                                            <router-link
                                                to="/relatorios/livros-por-categoria"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                                                @click="showRelatoriosMenu = false"
                                            >
                                                Livros por Categoria
                                            </router-link>
                                            <router-link
                                                to="/relatorios/por-autor"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                                                @click="showRelatoriosMenu = false"
                                            >
                                                Relatório por Autor
                                            </router-link>
                                        </div>
                                    </div>
                                </transition>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Menu mobile button -->
                    <div class="sm:hidden flex items-center">
                        <button
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                        >
                            <svg v-if="!mobileMenuOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div v-show="mobileMenuOpen" class="sm:hidden border-t border-gray-200">
                    <div class="pt-2 pb-3 space-y-1">
                        <router-link
                            to="/livros"
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors"
                            :class="route.path === '/livros' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300'"
                            @click="mobileMenuOpen = false"
                        >
                            Livros
                        </router-link>
                        <router-link
                            to="/autores"
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors"
                            :class="route.path === '/autores' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300'"
                            @click="mobileMenuOpen = false"
                        >
                            Autores
                        </router-link>
                        <router-link
                            to="/assuntos"
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors"
                            :class="route.path === '/assuntos' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300'"
                            @click="mobileMenuOpen = false"
                        >
                            Assuntos
                        </router-link>
                        <div class="border-t border-gray-200 pt-2">
                            <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Relatórios
                            </div>
                            <router-link
                                to="/relatorios/livros-por-categoria"
                                class="block pl-6 pr-4 py-2 text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors"
                                @click="mobileMenuOpen = false"
                            >
                                Livros por Categoria
                            </router-link>
                            <router-link
                                to="/relatorios/por-autor"
                                class="block pl-6 pr-4 py-2 text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors"
                                @click="mobileMenuOpen = false"
                            >
                                Relatório por Autor
                            </router-link>
                        </div>
                    </div>
                </div>
            </transition>
        </nav>

        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <router-view />
        </main>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const showRelatoriosMenu = ref(false);
const mobileMenuOpen = ref(false);

const isRelatorioActive = computed(() => {
    return route.path.startsWith('/relatorios');
});
</script>


