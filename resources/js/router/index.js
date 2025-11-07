import { createRouter, createWebHistory } from 'vue-router';
import LivrosList from '../pages/LivrosList.vue';
import LivroForm from '../pages/LivroForm.vue';
import AutoresList from '../pages/AutoresList.vue';
import AutorForm from '../pages/AutorForm.vue';
import AssuntosList from '../pages/AssuntosList.vue';
import AssuntoForm from '../pages/AssuntoForm.vue';
import RelatoriosLivrosPorCategoria from '../pages/RelatoriosLivrosPorCategoria.vue';
import RelatoriosPorAutor from '../pages/RelatoriosPorAutor.vue';

const routes = [
    {
        path: '/',
        redirect: '/livros'
    },
    {
        path: '/livros',
        name: 'livros',
        component: LivrosList
    },
    {
        path: '/livros/novo',
        name: 'livro.create',
        component: LivroForm
    },
    {
        path: '/livros/:id/editar',
        name: 'livro.edit',
        component: LivroForm,
        props: true
    },
    {
        path: '/autores',
        name: 'autores',
        component: AutoresList
    },
    {
        path: '/autores/novo',
        name: 'autor.create',
        component: AutorForm
    },
    {
        path: '/autores/:id/editar',
        name: 'autor.edit',
        component: AutorForm,
        props: true
    },
    {
        path: '/assuntos',
        name: 'assuntos',
        component: AssuntosList
    },
    {
        path: '/assuntos/novo',
        name: 'assunto.create',
        component: AssuntoForm
    },
    {
        path: '/assuntos/:id/editar',
        name: 'assunto.edit',
        component: AssuntoForm,
        props: true
    },
    {
        path: '/relatorios/livros-por-categoria',
        name: 'relatorios.livros-por-categoria',
        component: RelatoriosLivrosPorCategoria
    },
    {
        path: '/relatorios/por-autor',
        name: 'relatorios.por-autor',
        component: RelatoriosPorAutor
    },
];

const router = createRouter({
    history: createWebHistory('/app'),
    routes
});

export default router;

