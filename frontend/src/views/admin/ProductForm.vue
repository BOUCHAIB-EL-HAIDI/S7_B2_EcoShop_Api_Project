<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../../services/api';
import { ArrowLeft, Save, Upload, AlertCircle } from 'lucide-vue-next';

const route = useRoute();
const router = useRouter();
const isEdit = !!route.params.id;

const form = ref({
  name: '',
  description: '',
  price: '',
  stock: '',
  category_id: '',
  image: null
});

const categories = ref([]);
const loading = ref(false);
const error = ref(null);
const imagePreview = ref(null);

const fetchCategories = async () => {
  try {
    const response = await api.get('/categories');
    categories.value = response.data;
  } catch (err) {
    console.error('Error fetching categories', err);
  }
};

const fetchProduct = async () => {
  if (!isEdit) return;
  try {
    const response = await api.get(`/products/${route.params.id}`);
    const p = response.data;
    form.value = {
      name: p.name,
      description: p.description,
      price: p.price,
      stock: p.stock,
      category_id: p.category_id
    };
    imagePreview.value = p.image_url;
  } catch (err) {
    console.error('Error fetching product', err);
    router.push({ name: 'admin-products' });
  }
};

onMounted(() => {
  fetchCategories();
  fetchProduct();
});

const handleImageChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.value.image = file;
    imagePreview.value = URL.createObjectURL(file);
  }
};

const handleSubmit = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    const formData = new FormData();
    for (const key in form.value) {
      if (form.value[key] !== null) {
        formData.append(key, form.value[key]);
      }
    }
    
    // For PUT requests with FormData, Laravel sometimes needs _method: 'PUT'
    if (isEdit) {
      formData.append('_method', 'PUT');
      await api.post(`/products/${route.params.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    } else {
      await api.post('/products', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    }
    
    router.push({ name: 'admin-products' });
  } catch (err) {
    error.value = err.response?.data?.message || 'Une erreur est survenue lors de l\'enregistrement.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="admin-product-form container">
    <button @click="router.back()" class="back-btn">
      <ArrowLeft :size="20" /> Retour
    </button>

    <div class="form-header">
      <h1>{{ isEdit ? 'Modifier le Produit' : 'Nouveau Produit' }}</h1>
    </div>

    <div class="card form-card">
      <form @submit.prevent="handleSubmit" class="product-form">
        <div v-if="error" class="error-alert">
          <AlertCircle :size="18" />
          <span>{{ error }}</span>
        </div>

        <div class="form-layout">
          <div class="form-main">
            <div class="form-group">
              <label>Nom du produit</label>
              <input type="text" v-model="form.name" required placeholder="Ex: Savon Bio au Miel">
            </div>

            <div class="form-group">
              <label>Description</label>
              <textarea v-model="form.description" rows="5" required placeholder="Décrivez le produit..."></textarea>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Prix (€)</label>
                <input type="number" step="0.01" v-model="form.price" required placeholder="0.00">
              </div>
              <div class="form-group">
                <label>Stock</label>
                <input type="number" v-model="form.stock" required placeholder="0">
              </div>
              <div class="form-group">
                <label>Catégorie</label>
                <select v-model="form.category_id" required>
                  <option value="">Sélectionner</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                    {{ cat.name }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-side">
            <div class="image-upload-wrapper">
              <label>Image du produit</label>
              <div class="image-preview card" :class="{ 'has-image': imagePreview }">
                <img v-if="imagePreview" :src="imagePreview" alt="Preview">
                <div v-else class="upload-placeholder">
                  <Upload :size="32" />
                  <span>Cliquer pour uploader</span>
                </div>
                <input type="file" @change="handleImageChange" accept="image/*">
              </div>
            </div>
          </div>
        </div>

        <div class="form-actions">
          <button type="button" @click="router.back()" class="btn btn-outline">Annuler</button>
          <button type="submit" class="btn btn-primary" :disabled="loading">
            <span v-if="loading" class="btn-loader"></span>
            <Save v-else :size="18" /> 
            {{ isEdit ? 'Enregistrer les modifications' : 'Créer le produit' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.back-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--text-muted);
  margin-bottom: 2rem;
  font-weight: 500;
}

.form-header {
  margin-bottom: 2.5rem;
}

.form-card {
  padding: 2.5rem;
}

.product-form {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.form-layout {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 3rem;
}

.form-main {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-size: 0.875rem;
  font-weight: 600;
}

textarea {
  resize: vertical;
}

.image-upload-wrapper {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.image-upload-wrapper label {
  font-size: 0.875rem;
  font-weight: 600;
}

.image-preview {
  height: 300px;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  border: 2px dashed var(--border-color);
  background: var(--bg-color);
  transition: var(--transition);
}

.image-preview:hover {
  border-color: var(--primary);
}

.image-preview.has-image {
  border: none;
}

.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: var(--radius-lg);
}

.upload-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  color: var(--text-muted);
}

.image-preview input {
  position: absolute;
  inset: 0;
  opacity: 0;
  cursor: pointer;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1.5rem;
  padding-top: 2rem;
  border-top: 1px solid var(--border-color);
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
  .form-layout {
    grid-template-columns: 1fr;
  }
}
</style>
