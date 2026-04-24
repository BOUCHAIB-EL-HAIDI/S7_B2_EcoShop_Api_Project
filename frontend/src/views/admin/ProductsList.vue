<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';
import { Plus, Edit2, Trash2, Search, Filter } from 'lucide-vue-next';

const products = ref([]);
const loading = ref(true);
const searchTerm = ref('');

const fetchProducts = async () => {
  loading.value = true;
  try {
    const response = await api.get('/products');
    products.value = response.data.data || response.data;
  } catch (err) {
    console.error('Error fetching products', err);
  } finally {
    loading.value = false;
  }
};

const deleteProduct = async (id) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
    try {
      await api.delete(`/products/${id}`);
      products.value = products.value.filter(p => p.id !== id);
    } catch (err) {
      alert('Erreur lors de la suppression');
    }
  }
};

onMounted(fetchProducts);
</script>

<template>
  <div class="admin-products container">
    <div class="header-actions">
      <div>
        <h1>Gestion des Produits</h1>
        <p>Ajoutez, modifiez ou supprimez des produits de votre catalogue.</p>
      </div>
      <router-link to="/admin/products/create" class="btn btn-primary">
        <Plus :size="20" /> Nouveau Produit
      </router-link>
    </div>

    <div class="card table-card">
      <div class="table-tools">
        <div class="search-box">
          <Search :size="18" />
          <input type="text" v-model="searchTerm" placeholder="Rechercher...">
        </div>
      </div>

      <div v-if="loading" class="loader-container">
        <div class="loader"></div>
      </div>

      <div v-else class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Produit</th>
              <th>Catégorie</th>
              <th>Prix</th>
              <th>Stock</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products" :key="product.id">
              <td class="product-cell">
                <img :src="product.image_url || 'https://via.placeholder.com/50'" alt="">
                <span>{{ product.name }}</span>
              </td>
              <td>{{ product.category?.name }}</td>
              <td>{{ product.price }} €</td>
              <td>
                <span class="badge" :class="product.stock > 0 ? 'badge-success' : 'badge-danger'">
                  {{ product.stock }} en stock
                </span>
              </td>
              <td class="actions-cell">
                <router-link :to="`/admin/products/edit/${product.id}`" class="btn-icon edit" title="Modifier">
                  <Edit2 :size="18" />
                </router-link>
                <button @click="deleteProduct(product.id)" class="btn-icon delete" title="Supprimer">
                  <Trash2 :size="18" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
.header-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 3rem;
}

.table-card {
  padding: 1.5rem;
}

.table-tools {
  margin-bottom: 2rem;
}

.search-box {
  max-width: 300px;
  position: relative;
}

.search-box input {
  padding-left: 2.5rem;
  width: 100%;
}

.search-box svg {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
}

.product-cell {
  display: flex;
  align-items: center;
  gap: 1rem;
  font-weight: 600;
}

.product-cell img {
  width: 40px;
  height: 40px;
  border-radius: var(--radius-sm);
  object-fit: cover;
}

.actions-cell {
  display: flex;
  gap: 0.5rem;
}

.btn-icon {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: var(--radius-sm);
  transition: var(--transition);
}

.btn-icon.edit { background: var(--primary-light); color: var(--primary-dark); }
.btn-icon.delete { background: #fee2e2; color: var(--danger); }

.btn-icon:hover { transform: translateY(-2px); box-shadow: var(--shadow-sm); }

.loader-container {
  display: flex;
  justify-content: center;
  padding: 3rem 0;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  text-align: left;
  padding: 1rem;
  font-size: 0.75rem;
  text-transform: uppercase;
  color: var(--text-muted);
  border-bottom: 1px solid var(--border-color);
}

td {
  padding: 1rem;
  border-bottom: 1px solid var(--border-color);
  font-size: 0.875rem;
}
</style>
