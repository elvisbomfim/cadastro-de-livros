import api from './api.js';
import axios from 'axios';

const pdfApi = axios.create({
    baseURL: '',
    headers: {
        'Accept': 'application/pdf',
    },
    responseType: 'blob'
});

export const relatorioService = {
    livrosPorCategoria() {
        return api.get('/relatorios/livros-por-categoria');
    },
    fichaDetalhadaLivro(id) {
        return api.get(`/relatorios/livro/${id}/ficha`);
    },
    relatorioPorAutor() {
        return api.get('/relatorios/por-autor');
    },
    downloadLivrosPorCategoriaPdf() {
        return pdfApi.get('/relatorios/livros-por-categoria/pdf');
    },
    downloadFichaLivroPdf(id) {
        return pdfApi.get(`/relatorios/livro/${id}/ficha/pdf`);
    },
    downloadRelatorioPorAutorPdf() {
        return pdfApi.get('/relatorios/por-autor/pdf');
    }
};

