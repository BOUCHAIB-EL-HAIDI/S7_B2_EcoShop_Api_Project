<script setup>
import { onMounted } from 'vue';
import { useCartStore } from '../stores/cart';
import { ShoppingBag, Trash2, Plus, Minus, ArrowRight, ShoppingCart } from 'lucide-vue-next';
import { useRouter } from 'vue-router';

const cartStore = useCartStore();
const router = useRouter();

onMounted(async () => {
  await cartStore.fetchCart();
});

const updateQuantity = async (item, delta) => {
  const newQty = item.quantity + delta;
  if (newQty >= 1) {
    await cartStore.updateQuantity(item.id, newQty);
  }
};

const removeItem = async (id) => {
  if (confirm('Voulez-vous retirer cet article du panier ?')) {
    await cartStore.removeFromCart(id);
  }
};
</script>

<template>
  <div class="cart-view container">
    <div class="cart-header">
      <ShoppingBag :size="32" class="icon" />
      <h1>Mon Panier</h1>
      <span class="count">({{ cartStore.totalItems }} articles)</span>
    </div>

    <div v-if="cartStore.loading" class="loader-container">
      <div class="loader"></div>
    </div>

    <template v-else>
      <div v-if="cartStore.items.length > 0" class="cart-content">
        <div class="cart-items">
          <div v-for="item in cartStore.items" :key="item.id" class="cart-item card">
            <div class="item-image">
              <img :src="item.product.image_url || 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?q=80&w=2013&auto=format&fit=crop'" :alt="item.product.name">
            </div>
            
            <div class="item-details">
              <div class="item-info">
                <h3>{{ item.product.name }}</h3>
                <p class="item-category">{{ item.product.category?.name }}</p>
                <p class="item-price-unit">{{ item.product.price }} € / unité</p>
              </div>

              <div class="item-actions">
                <div class="quantity-control">
                  <button @click="updateQuantity(item, -1)" class="qty-btn" :disabled="item.quantity <= 1">
                    <Minus :size="16" />
                  </button>
                  <span class="qty-value">{{ item.quantity }}</span>
                  <button @click="updateQuantity(item, 1)" class="qty-btn">
                    <Plus :size="16" />
                  </button>
                </div>

                <div class="item-total">
                  {{ (item.product.price * item.quantity).toFixed(2) }} €
                </div>

                <button @click="removeItem(item.id)" class="delete-btn" title="Supprimer">
                  <Trash2 :size="18" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <aside class="cart-summary">
          <div class="card summary-card">
            <h3>Récapitulatif</h3>
            <div class="summary-row">
              <span>Sous-total</span>
              <span>{{ cartStore.totalPrice.toFixed(2) }} €</span>
            </div>
            <div class="summary-row">
              <span>Livraison</span>
              <span class="free">Gratuite</span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-row total">
              <span>Total</span>
              <span>{{ cartStore.totalPrice.toFixed(2) }} €</span>
            </div>
            
            <router-link to="/checkout" class="btn btn-primary btn-full btn-xl">
              Commander <ArrowRight :size="18" />
            </router-link>
            
            <p class="summary-info">Taxes incluses. Paiement sécurisé.</p>
          </div>
        </aside>
      </div>

      <div v-else class="empty-cart card">
        <ShoppingCart :size="64" class="empty-icon" />
        <h2>Votre panier est vide</h2>
        <p>Il semblerait que vous n'ayez pas encore ajouté de produits éco-responsables.</p>
        <router-link to="/" class="btn btn-primary">
          Parcourir les produits
        </router-link>
      </div>
    </template>
  </div>
</template>

<style scoped>
.cart-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 3rem;
}

.cart-header .icon {
  color: var(--primary);
}

.cart-header h1 {
  font-size: 2rem;
}

.cart-header .count {
  color: var(--text-muted);
  font-size: 1.25rem;
  font-weight: 500;
}

.cart-content {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 3rem;
  align-items: start;
}

.cart-items {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.cart-item {
  display: flex;
  padding: 1rem;
  gap: 1.5rem;
}

.item-image {
  width: 120px;
  height: 120px;
  border-radius: var(--radius);
  overflow: hidden;
  flex-shrink: 0;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-details {
  flex: 1;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.item-info h3 {
  font-size: 1.125rem;
  margin-bottom: 0.25rem;
}

.item-category {
  font-size: 0.875rem;
  color: var(--secondary);
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.item-price-unit {
  font-size: 0.875rem;
  color: var(--text-muted);
}

.item-actions {
  display: flex;
  align-items: center;
  gap: 3rem;
}

.quantity-control {
  display: flex;
  align-items: center;
  background: var(--bg-color);
  border-radius: var(--radius);
  padding: 0.25rem;
}

.qty-btn {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: var(--radius-sm);
  color: var(--text-main);
}

.qty-btn:hover:not(:disabled) {
  background: white;
  color: var(--primary);
  box-shadow: var(--shadow-sm);
}

.qty-value {
  width: 40px;
  text-align: center;
  font-weight: 600;
}

.item-total {
  font-weight: 700;
  font-size: 1.125rem;
  color: var(--text-main);
  min-width: 80px;
  text-align: right;
}

.delete-btn {
  color: var(--text-muted);
  transition: var(--transition);
}

.delete-btn:hover {
  color: var(--danger);
}

.summary-card {
  padding: 2rem;
  position: sticky;
  top: 100px;
}

.summary-card h3 {
  margin-bottom: 1.5rem;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
  color: var(--text-muted);
}

.summary-row .free {
  color: var(--success);
  font-weight: 600;
}

.summary-divider {
  height: 1px;
  background: var(--border-color);
  margin: 1.5rem 0;
}

.summary-row.total {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text-main);
  margin-bottom: 2rem;
}

.btn-xl {
  height: 3.5rem;
  font-size: 1.125rem;
}

.summary-info {
  margin-top: 1.5rem;
  text-align: center;
  font-size: 0.75rem;
  color: var(--text-muted);
}

.empty-cart {
  padding: 5rem;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.5rem;
}

.empty-icon {
  color: var(--border-color);
}

.empty-cart h2 {
  font-size: 2rem;
}

.empty-cart p {
  color: var(--text-muted);
  max-width: 400px;
  margin-bottom: 1rem;
}

.loader-container {
  display: flex;
  justify-content: center;
  padding: 5rem 0;
}

@media (max-width: 992px) {
  .cart-content {
    grid-template-columns: 1fr;
  }
  
  .item-details {
    flex-direction: column;
    align-items: flex-start;
    gap: 1.5rem;
  }
  
  .item-actions {
    width: 100%;
    justify-content: space-between;
    gap: 1rem;
  }
}
</style>
