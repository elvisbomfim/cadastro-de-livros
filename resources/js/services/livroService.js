import api from './api.js';

export const livroService = {
    async list(params = {}) {
        const response = await api.get('/livros', { params });
        return response.data;
    },

    async get(id) {
        const response = await api.get(`/livros/${id}`);
        return response.data;
    },

    async create(data) {
        const response = await api.post('/livros', data);
        return response.data;
    },

    async update(id, data) {
        const response = await api.put(`/livros/${id}`, data);
        return response.data;
    },

    async delete(id) {
        await api.delete(`/livros/${id}`);
    },
};

