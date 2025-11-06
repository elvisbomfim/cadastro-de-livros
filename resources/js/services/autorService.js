import api from './api.js';

export const autorService = {
    async list() {
        const response = await api.get('/autores');
        return response.data;
    },

    async get(id) {
        const response = await api.get(`/autores/${id}`);
        return response.data;
    },

    async create(data) {
        const response = await api.post('/autores', data);
        return response.data;
    },

    async update(id, data) {
        const response = await api.put(`/autores/${id}`, data);
        return response.data;
    },

    async delete(id) {
        await api.delete(`/autores/${id}`);
    },
};

