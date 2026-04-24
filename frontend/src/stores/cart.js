import { defineStore } from 'pinia';
import api from '../services/api';

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: [],
        loading: false,
    }),

    getters: {
        totalItems: (state) => state.items.reduce((sum, item) => sum + item.quantity, 0),
        totalPrice: (state) => state.items.reduce((sum, item) => sum + (item.product.price * item.quantity), 0),
    },

    actions: {
        async fetchCart() {
            this.loading = true;
            try {
                const response = await api.get('/cart');
                this.items = response.data;
            } catch (err) {
                console.error('Failed to fetch cart', err);
            } finally {
                this.loading = false;
            }
        },

        async addToCart(productId, quantity = 1) {
            try {
                await api.post('/cart', { product_id: productId, quantity });
                await this.fetchCart();
            } catch (err) {
                console.error('Failed to add to cart', err);
            }
        },

        async updateQuantity(cartItemId, quantity) {
            try {
                await api.put(`/cart/${cartItemId}`, { quantity });
                await this.fetchCart();
            } catch (err) {
                console.error('Failed to update quantity', err);
            }
        },

        async removeFromCart(cartItemId) {
            try {
                await api.delete(`/cart/${cartItemId}`);
                await this.fetchCart();
            } catch (err) {
                console.error('Failed to remove from cart', err);
            }
        },

        clearCart() {
            this.items = [];
        }
    },
});
