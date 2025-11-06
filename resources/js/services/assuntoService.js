import api from './api.js';

export const assuntoService = {
    async list() {
        const response = await api.get('/assuntos');
        return response.data;
    },

    async get(id) {
        const response = await api.get(`/assuntos/${id}`);
        return response.data;
    },

    async create(data) {
        const response = await api.post('/assuntos', data);
        return response.data;
    },

    async update(id, data) {
        const response = await api.put(`/assuntos/${id}`, data);
        return response.data;
    },

    async delete(id) {
        await api.delete(`/assuntos/${id}`);
    },
};

