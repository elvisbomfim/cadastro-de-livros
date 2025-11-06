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
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <router-view />
        </main>
    </div>
</template>

<script setup>
</script>


