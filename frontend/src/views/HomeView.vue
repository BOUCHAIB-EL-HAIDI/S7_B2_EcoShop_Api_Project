<script setup>
import { ref, onMounted, watch } from 'vue';
import api from '../services/api';
import { useCartStore } from '../stores/cart';
import { ShoppingCart, Eye, Search, Filter } from 'lucide-vue-next';

const products = ref([]);
const categories = ref([]);
const loading = ref(true);
const cartStore = useCartStore();

const filters = ref({
  category_id: '',
  search: '',
  page: 1
});

const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0
});

const fetchCategories = async () => {
  try {
    const response = await api.get('/categories');
    categories.value = response.data;
  } catch (err) {
    console.error('Error fetching categories', err);
  }
};

const fetchProducts = async () => {
  loading.value = true;
  try {
    const params = {
      category_id: filters.value.category_id,
      search: filters.value.search,
      page: filters.value.page
    };
    const response = await api.get('/products', { params });
    products.value = response.data.data;
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total
    };
  } catch (err) {
    console.error('Error fetching products', err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchCategories();
  fetchProducts();
});

watch(() => filters.value.category_id, () => {
  filters.value.page = 1;
  fetchProducts();
});

watch(() => filters.value.page, () => {
  fetchProducts();
});

const handleSearch = () => {
  filters.value.page = 1;
  fetchProducts();
};

const addToCart = async (product) => {
  await cartStore.addToCart(product.id, 1);
};
</script>

<template>
  <div class="home-view container">
    <header class="hero">
      <div class="hero-content">
        <h1>Découvrez notre sélection <span class="text-gradient">Éco-responsable</span></h1>
        <p>Des produits de qualité qui respectent la planète et votre santé.</p>
      </div>
    </header>

    <section class="filters-bar card">
      <div class="search-wrapper">
        <Search :size="18" class="search-icon" />
        <input 
          type="text" 
          v-model="filters.search" 
          placeholder="Rechercher un produit..." 
          @keyup.enter="handleSearch"
        />
      </div>

      <div class="category-wrapper">
        <Filter :size="18" />
        <select v-model="filters.category_id">
          <option value="">Toutes les catégories</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
      </div>
    </section>

    <div v-if="loading" class="loader-container">
      <div class="loader"></div>
    </div>

    <template v-else>
      <div v-if="products.length > 0" class="product-grid">
        <div v-for="product in products" :key="product.id" class="product-card card animate-fade-in">
          <div class="product-image">
            <img :src="product.image_url || 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?q=80&w=2013&auto=format&fit=crop'" :alt="product.name">
            <div class="product-actions">
              <router-link :to="`/product/${product.id}`" class="action-btn" title="Voir le détail">
                <Eye :size="20" />
              </router-link>
            </div>
          </div>
          <div class="product-info">
            <div class="product-meta">
              <span class="category-tag">{{ product.category?.name }}</span>
              <span v-if="product.stock > 0" class="badge badge-success">En stock</span>
              <span v-else class="badge badge-danger">Épuisé</span>
            </div>
            <h3 class="product-name">{{ product.name }}</h3>
            <p class="product-price">{{ product.price }} €</p>
            <button 
              @click="addToCart(product)" 
              class="btn btn-primary btn-full"
              :disabled="product.stock <= 0"
            >
              <ShoppingCart :size="18" /> Ajouter au panier
            </button>
          </div>
        </div>
      </div>

      <div v-else class="empty-state">
        <p>Aucun produit trouvé.</p>
      </div>

      <div v-if="pagination.last_page > 1" class="pagination">
        <button 
          v-for="page in pagination.last_page" 
          :key="page"
          class="page-btn"
          :class="{ active: pagination.current_page === page }"
          @click="filters.page = page"
        >
          {{ page }}
        </button>
      </div>
    </template>
  </div>
</template>

<style scoped>
.hero {
  padding: 4rem 0;
  text-align: center;
}

.hero h1 {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.text-gradient {
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.hero p {
  color: var(--text-muted);
  font-size: 1.25rem;
}

.filters-bar {
  display: flex;
  gap: 2rem;
  padding: 1.5rem;
  margin-bottom: 3rem;
  align-items: center;
  flex-wrap: wrap;
}

.search-wrapper {
  flex: 1;
  position: relative;
  min-width: 250px;
}

.search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
}

.search-wrapper input {
  width: 100%;
  padding-left: 3rem;
}

.category-wrapper {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: var(--text-muted);
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
}

.product-card {
  display: flex;
  flex-direction: column;
}

.product-image {
  position: relative;
  height: 250px;
  overflow: hidden;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: var(--transition);
}

.product-card:hover .product-image img {
  transform: scale(1.05);
}

.product-actions {
  position: absolute;
  top: 1rem;
  right: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  opacity: 0;
  transform: translateX(10px);
  transition: var(--transition);
}

.product-card:hover .product-actions {
  opacity: 1;
  transform: translateX(0);
}

.action-btn {
  background: white;
  color: var(--text-main);
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: var(--shadow);
}

.action-btn:hover {
  color: var(--primary);
}

.product-info {
  padding: 1.5rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.product-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.category-tag {
  font-size: 0.75rem;
  color: var(--secondary);
  font-weight: 600;
  text-transform: uppercase;
}

.product-name {
  font-size: 1.125rem;
  margin-bottom: 0.5rem;
}

.product-price {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 1.5rem;
}

.btn-full {
  width: 100%;
  margin-top: auto;
}

.pagination {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 4rem;
}

.page-btn {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: var(--radius);
  border: 1px solid var(--border-color);
  background: white;
}

.page-btn.active {
  background: var(--primary);
  color: white;
  border-color: var(--primary);
}

.loader-container {
  display: flex;
  justify-content: center;
  padding: 4rem 0;
}

.loader {
  width: 40px;
  height: 40px;
  border: 4px solid var(--primary-light);
  border-top: 4px solid var(--primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@media (max-width: 640px) {
  .hero h1 { font-size: 2rem; }
  .filters-bar { flex-direction: column; align-items: stretch; }
}
</style>
