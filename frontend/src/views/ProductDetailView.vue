<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../services/api';
import { useCartStore } from '../stores/cart';
import { ShoppingCart, ArrowLeft, ShieldCheck, Truck, RotateCcw } from 'lucide-vue-next';

const route = useRoute();
const router = useRouter();
const product = ref(null);
const loading = ref(true);
const quantity = ref(1);
const cartStore = useCartStore();

const fetchProduct = async () => {
  try {
    const response = await api.get(`/products/${route.params.id}`);
    product.value = response.data;
  } catch (err) {
    console.error('Error fetching product', err);
    router.push({ name: 'home' });
  } finally {
    loading.value = false;
  }
};

onMounted(fetchProduct);

const addToCart = async () => {
  await cartStore.addToCart(product.value.id, quantity.value);
};
</script>

<template>
  <div class="product-detail container">
    <button @click="router.back()" class="back-btn">
      <ArrowLeft :size="20" /> Retour aux produits
    </button>

    <div v-if="loading" class="loader-container">
      <div class="loader"></div>
    </div>

    <div v-else-if="product" class="product-layout animate-fade-in">
      <div class="product-gallery">
        <div class="main-image card">
          <img :src="product.image_url || 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?q=80&w=2013&auto=format&fit=crop'" :alt="product.name">
        </div>
      </div>

      <div class="product-content">
        <div class="product-header">
          <span class="category-tag">{{ product.category?.name }}</span>
          <h1>{{ product.name }}</h1>
          <p class="price">{{ product.price }} €</p>
        </div>

        <div class="stock-status">
          <span v-if="product.stock > 0" class="badge badge-success">
            En stock ({{ product.stock }} disponibles)
          </span>
          <span v-else class="badge badge-danger">Actuellement épuisé</span>
        </div>

        <div class="description">
          <h3>Description</h3>
          <p>{{ product.description }}</p>
        </div>

        <div class="purchase-actions" v-if="product.stock > 0">
          <div class="quantity-selector">
            <label>Quantité</label>
            <input type="number" v-model="quantity" min="1" :max="product.stock">
          </div>
          <button @click="addToCart" class="btn btn-primary btn-xl">
            <ShoppingCart :size="20" /> Ajouter au panier
          </button>
        </div>

        <div class="product-features">
          <div class="feature">
            <ShieldCheck :size="24" class="icon" />
            <div>
              <h4>Produit Naturel</h4>
              <p>Certifié 100% biologique et durable.</p>
            </div>
          </div>
          <div class="feature">
            <Truck :size="24" class="icon" />
            <div>
              <h4>Livraison Rapide</h4>
              <p>Expédition sous 24/48h en France.</p>
            </div>
          </div>
          <div class="feature">
            <RotateCcw :size="24" class="icon" />
            <div>
              <h4>Retours Gratuits</h4>
              <p>30 jours pour changer d'avis.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.product-detail {
  padding-bottom: 5rem;
}

.back-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--text-muted);
  margin-bottom: 2rem;
  font-weight: 500;
}

.back-btn:hover {
  color: var(--primary);
}

.product-layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: start;
}

.main-image {
  aspect-ratio: 1;
  border-radius: var(--radius-lg);
  overflow: hidden;
}

.main-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-header {
  margin-bottom: 1.5rem;
}

.category-tag {
  color: var(--secondary);
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.875rem;
  display: block;
  margin-bottom: 0.5rem;
}

.product-header h1 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.price {
  font-size: 2rem;
  font-weight: 700;
  color: var(--primary);
}

.stock-status {
  margin-bottom: 2rem;
}

.description {
  margin-bottom: 2.5rem;
}

.description h3 {
  font-size: 1.125rem;
  margin-bottom: 0.75rem;
}

.description p {
  color: var(--text-muted);
  line-height: 1.7;
}

.purchase-actions {
  display: flex;
  gap: 1.5rem;
  align-items: flex-end;
  margin-bottom: 3rem;
  padding-bottom: 3rem;
  border-bottom: 1px solid var(--border-color);
}

.quantity-selector {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.quantity-selector label {
  font-size: 0.875rem;
  font-weight: 600;
}

.quantity-selector input {
  width: 80px;
  text-align: center;
}

.btn-xl {
  flex: 1;
  height: 3.5rem;
  font-size: 1.125rem;
}

.product-features {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.feature {
  display: flex;
  gap: 1rem;
}

.feature .icon {
  color: var(--primary);
  flex-shrink: 0;
}

.feature h4 {
  font-size: 1rem;
  margin-bottom: 0.25rem;
}

.feature p {
  font-size: 0.875rem;
  color: var(--text-muted);
}

.loader-container {
  display: flex;
  justify-content: center;
  padding: 5rem 0;
}

@media (max-width: 992px) {
  .product-layout {
    grid-template-columns: 1fr;
    gap: 2.5rem;
  }
}
</style>
