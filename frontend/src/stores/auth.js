import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token') || null,
        loading: false,
        error: null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        isAdmin: (state) => state.user?.role === 'admin',
    },

    actions: {
        async login(credentials) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.post('/login', credentials);
                this.token = response.data.token;
                this.user = response.data.user;
                localStorage.setItem('token', this.token);
                return response;
            } catch (err) {
                this.error = err.response?.data?.message || 'Login failed';
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async register(userData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.post('/register', userData);
                this.token = response.data.token;
                this.user = response.data.user;
                localStorage.setItem('token', this.token);
                return response;
            } catch (err) {
                this.error = err.response?.data?.message || 'Registration failed';
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            try {
                await api.post('/logout');
            } catch (err) {
                console.error('Logout error', err);
            } finally {
                this.user = null;
                this.token = null;
                localStorage.removeItem('token');
            }
        },

        async fetchUser() {
            if (!this.token) return;
            try {
                const response = await api.get('/user');
                this.user = response.data;
            } catch (err) {
                this.logout();
            }
        },
    },
});
