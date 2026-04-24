<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';
import { Plus, Edit2, Trash2, Save, X, AlertCircle } from 'lucide-vue-next';

const categories = ref([]);
const loading = ref(true);
const saving = ref(false);
const error = ref(null);

const editingId = ref(null);
const form = ref({
  name: ''
});

const fetchCategories = async () => {
  loading.value = true;
  try {
    const response = await api.get('/categories');
    categories.value = response.data;
  } catch (err) {
    console.error('Error fetching categories', err);
  } finally {
    loading.value = false;
  }
};

const startEdit = (category) => {
  editingId.value = category.id;
  form.value.name = category.name;
};

const cancelEdit = () => {
  editingId.value = null;
  form.value.name = '';
  error.value = null;
};

const handleSubmit = async () => {
  saving.value = true;
  error.value = null;
  try {
    if (editingId.value) {
      await api.put(`/categories/${editingId.value}`, form.value);
    } else {
      await api.post('/categories', form.value);
    }
    await fetchCategories();
    cancelEdit();
  } catch (err) {
    error.value = err.response?.data?.message || 'Une erreur est survenue.';
  } finally {
    saving.value = false;
  }
};

const deleteCategory = async (id) => {
  if (confirm('Êtes-vous sûr ? Cela supprimera également tous les produits associés.')) {
    try {
      await api.delete(`/categories/${id}`);
      await fetchCategories();
    } catch (err) {
      alert('Erreur lors de la suppression');
    }
  }
};

onMounted(fetchCategories);
</script>

<template>
  <div class="admin-categories container">
    <div class="header-actions">
      <div>
        <h1>Gestion des Catégories</h1>
        <p>Gérez les catégories de produits de votre boutique.</p>
      </div>
    </div>

    <div class="category-layout">
      <!-- Add/Edit Form -->
      <div class="card form-card">
        <h3>{{ editingId ? 'Modifier la catégorie' : 'Ajouter une catégorie' }}</h3>
        <form @submit.prevent="handleSubmit" class="category-form">
          <div v-if="error" class="error-alert">
            <AlertCircle :size="18" />
            <span>{{ error }}</span>
          </div>

          <div class="form-group">
            <label>Nom de la catégorie</label>
            <input type="text" v-model="form.name" required placeholder="Ex: Cosmétiques">
          </div>

          <div class="form-actions">
            <button v-if="editingId" type="button" @click="cancelEdit" class="btn btn-outline">
              <X :size="18" /> Annuler
            </button>
            <button type="submit" class="btn btn-primary" :disabled="saving">
              <span v-if="saving" class="btn-loader"></span>
              <Save v-else :size="18" /> 
              {{ editingId ? 'Enregistrer' : 'Ajouter' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Categories List -->
      <div class="card table-card">
        <div v-if="loading" class="loader-container">
          <div class="loader"></div>
        </div>

        <div v-else class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Nom</th>
                <th>Nombre de produits</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="cat in categories" :key="cat.id" :class="{ 'is-editing': editingId === cat.id }">
                <td class="name-cell">{{ cat.name }}</td>
                <td>{{ cat.products?.length || 0 }} produits</td>
                <td class="actions-cell">
                  <button @click="startEdit(cat)" class="btn-icon edit" title="Modifier">
                    <Edit2 :size="18" />
                  </button>
                  <button @click="deleteCategory(cat.id)" class="btn-icon delete" title="Supprimer">
                    <Trash2 :size="18" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.header-actions {
  margin-bottom: 3rem;
}

.category-layout {
  display: grid;
  grid-template-columns: 350px 1fr;
  gap: 2rem;
  align-items: start;
}

.form-card {
  padding: 2rem;
}

.form-card h3 {
  margin-bottom: 1.5rem;
}

.category-form {
  display: flex;
  flex-direction: column;
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

.form-actions {
  display: flex;
  gap: 1rem;
}

.table-card {
  padding: 1.5rem;
}

.table-wrapper {
  overflow-x: auto;
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
  padding: 1.25rem 1rem;
  border-bottom: 1px solid var(--border-color);
  font-size: 0.875rem;
}

.name-cell {
  font-weight: 600;
}

.is-editing {
  background: var(--primary-light);
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
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@media (max-width: 992px) {
  .category-layout {
    grid-template-columns: 1fr;
  }
}
</style>
