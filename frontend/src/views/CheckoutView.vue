<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore } from '../stores/cart';
import api from '../services/api';
import { CheckCircle, Truck, CreditCard, AlertCircle, ArrowLeft } from 'lucide-vue-next';

const cartStore = useCartStore();
const router = useRouter();
const loading = ref(false);
const success = ref(false);
const error = ref(null);

const form = ref({
  address: '',
  city: '',
  postal_code: '',
  phone: ''
});

onMounted(async () => {
  if (cartStore.items.length === 0) {
    await cartStore.fetchCart();
    if (cartStore.items.length === 0) {
      router.push({ name: 'cart' });
    }
  }
});

const handleSubmit = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await api.post('/orders', {
      ...form.value,
      items: cartStore.items.map(item => ({
        product_id: item.product_id,
        quantity: item.quantity
      }))
    });
    
    success.value = true;
    cartStore.clearCart();
    // In a real app, the API would clear the cart on the backend too
  } catch (err) {
    error.value = err.response?.data?.message || 'Une erreur est survenue lors de la commande.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="checkout-view container">
    <template v-if="!success">
      <div class="checkout-header">
        <h1>Finaliser ma commande</h1>
        <p>Plus qu'une étape pour recevoir vos produits éco-responsables.</p>
      </div>

      <div class="checkout-layout">
        <form @submit.prevent="handleSubmit" class="checkout-form">
          <div class="card form-card">
            <div class="card-header">
              <Truck :size="20" />
              <h3>Informations de livraison</h3>
            </div>
            
            <div class="form-grid">
              <div class="form-group full">
                <label>Adresse complète</label>
                <input type="text" v-model="form.address" required placeholder="123 Rue de l'Écologie">
              </div>
              <div class="form-group">
                <label>Ville</label>
                <input type="text" v-model="form.city" required placeholder="Paris">
              </div>
              <div class="form-group">
                <label>Code Postal</label>
                <input type="text" v-model="form.postal_code" required placeholder="75001">
              </div>
              <div class="form-group full">
                <label>Téléphone</label>
                <input type="tel" v-model="form.phone" required placeholder="06 12 34 56 78">
              </div>
            </div>
          </div>

          <div class="card form-card">
            <div class="card-header">
              <CreditCard :size="20" />
              <h3>Mode de paiement</h3>
            </div>
            <p class="payment-note">Paiement à la livraison (Contre-remboursement)</p>
          </div>
        </form>

        <aside class="checkout-recap">
          <div class="card recap-card">
            <h3>Votre commande</h3>
            <div class="recap-items">
              <div v-for="item in cartStore.items" :key="item.id" class="recap-item">
                <span class="item-name">{{ item.quantity }}x {{ item.product.name }}</span>
                <span class="item-price">{{ (item.product.price * item.quantity).toFixed(2) }} €</span>
              </div>
            </div>
            
            <div class="recap-divider"></div>
            
            <div class="recap-row">
              <span>Sous-total</span>
              <span>{{ cartStore.totalPrice.toFixed(2) }} €</span>
            </div>
            <div class="recap-row">
              <span>Livraison</span>
              <span class="free">Gratuite</span>
            </div>
            
            <div class="recap-divider"></div>
            
            <div class="recap-row total">
              <span>Total à payer</span>
              <span>{{ cartStore.totalPrice.toFixed(2) }} €</span>
            </div>

            <div v-if="error" class="error-alert">
              <AlertCircle :size="18" />
              <span>{{ error }}</span>
            </div>

            <button @click="handleSubmit" class="btn btn-primary btn-full btn-xl" :disabled="loading">
              <span v-if="loading" class="btn-loader"></span>
              <span v-else>Confirmer la commande</span>
            </button>
            
            <router-link to="/cart" class="back-link">
              <ArrowLeft :size="16" /> Modifier mon panier
            </router-link>
          </div>
        </aside>
      </div>
    </template>

    <div v-else class="success-page animate-fade-in">
      <div class="success-card card">
        <div class="success-icon">
          <CheckCircle :size="64" />
        </div>
        <h2>Merci pour votre commande !</h2>
        <p>Votre commande a été validée avec succès. Un email de confirmation vient de vous être envoyé.</p>
        <div class="order-info">
          <p>Notre équipe prépare vos produits avec soin. Vous recevrez un nouvel email dès que votre colis sera expédié.</p>
        </div>
        <router-link to="/" class="btn btn-primary">
          Retour à la boutique
        </router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
.checkout-header {
  margin-bottom: 3rem;
}

.checkout-header h1 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.checkout-header p {
  color: var(--text-muted);
}

.checkout-layout {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 3rem;
  align-items: start;
}

.form-card {
  padding: 2rem;
  margin-bottom: 2rem;
}

.card-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 2rem;
  color: var(--primary);
}

.card-header h3 {
  color: var(--text-main);
  margin: 0;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group.full {
  grid-column: span 2;
}

.form-group label {
  font-size: 0.875rem;
  font-weight: 600;
}

.payment-note {
  padding: 1rem;
  background: var(--primary-light);
  color: var(--primary-dark);
  border-radius: var(--radius);
  font-weight: 500;
}

.recap-card {
  padding: 2rem;
  position: sticky;
  top: 100px;
}

.recap-card h3 {
  margin-bottom: 1.5rem;
}

.recap-items {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.recap-item {
  display: flex;
  justify-content: space-between;
  font-size: 0.875rem;
}

.item-name {
  color: var(--text-muted);
}

.item-price {
  font-weight: 600;
}

.recap-divider {
  height: 1px;
  background: var(--border-color);
  margin: 1.5rem 0;
}

.recap-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
  color: var(--text-muted);
}

.recap-row.total {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text-main);
  margin-bottom: 2rem;
}

.recap-row .free {
  color: var(--success);
  font-weight: 600;
}

.btn-xl {
  height: 3.5rem;
  font-size: 1.125rem;
  margin-bottom: 1rem;
}

.back-link {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  color: var(--text-muted);
  font-size: 0.875rem;
}

.back-link:hover {
  color: var(--primary);
}

.error-alert {
  background: #fee2e2;
  color: var(--danger);
  padding: 1rem;
  border-radius: var(--radius);
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.875rem;
  margin-bottom: 1.5rem;
}

.success-page {
  display: flex;
  justify-content: center;
  padding: 4rem 0;
}

.success-card {
  max-width: 600px;
  padding: 4rem;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.5rem;
}

.success-icon {
  color: var(--success);
  margin-bottom: 1rem;
}

.success-card h2 {
  font-size: 2.5rem;
}

.success-card p {
  color: var(--text-muted);
  font-size: 1.125rem;
}

.order-info {
  background: var(--bg-color);
  padding: 1.5rem;
  border-radius: var(--radius);
  margin: 1rem 0;
}

.btn-loader {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@media (max-width: 992px) {
  .checkout-layout {
    grid-template-columns: 1fr;
  }
}
</style>
