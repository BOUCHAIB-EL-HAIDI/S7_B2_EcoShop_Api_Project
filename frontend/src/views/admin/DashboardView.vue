<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';
import { Package, ShoppingCart, Users, TrendingUp, Calendar, ArrowUpRight } from 'lucide-vue-next';

const orders = ref([]);
const stats = ref({
  total_orders: 0,
  total_revenue: 0,
  total_products: 0,
  total_users: 0
});
const loading = ref(true);

const fetchData = async () => {
  try {
    const ordersRes = await api.get('/orders');
    orders.value = ordersRes.data;
    
    // In a real app, stats would come from a dedicated endpoint
    stats.value = {
      total_orders: orders.value.length,
      total_revenue: orders.value.reduce((sum, o) => sum + parseFloat(o.total_price || 0), 0),
      total_products: 0, // Would fetch products count
      total_users: 0 // Would fetch users count
    };
  } catch (err) {
    console.error('Error fetching admin data', err);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchData);

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'completed': return 'badge-success';
    case 'pending': return 'badge-warning';
    case 'cancelled': return 'badge-danger';
    default: return 'badge-info';
  }
};
</script>

<template>
  <div class="admin-dashboard container">
    <div class="dashboard-header">
      <h1>Tableau de Bord</h1>
      <p>Gérez vos commandes et surveillez les performances de votre boutique.</p>
    </div>

    <div class="stats-grid">
      <div class="stat-card card">
        <div class="stat-icon orders">
          <ShoppingCart :size="24" />
        </div>
        <div class="stat-info">
          <p>Total Commandes</p>
          <h3>{{ stats.total_orders }}</h3>
        </div>
        <div class="stat-trend positive">
          <TrendingUp :size="16" /> +12%
        </div>
      </div>

      <div class="stat-card card">
        <div class="stat-icon revenue">
          <TrendingUp :size="24" />
        </div>
        <div class="stat-info">
          <p>Chiffre d'Affaires</p>
          <h3>{{ stats.total_revenue.toFixed(2) }} €</h3>
        </div>
        <div class="stat-trend positive">
          <TrendingUp :size="16" /> +8%
        </div>
      </div>

      <div class="stat-card card">
        <div class="stat-icon products">
          <Package :size="24" />
        </div>
        <div class="stat-info">
          <p>Produits Actifs</p>
          <h3>{{ stats.total_products }}</h3>
        </div>
      </div>

      <div class="stat-card card">
        <div class="stat-icon users">
          <Users :size="24" />
        </div>
        <div class="stat-info">
          <p>Nouveaux Clients</p>
          <h3>{{ stats.total_users }}</h3>
        </div>
      </div>
    </div>

    <div class="dashboard-content">
      <div class="card table-card">
        <div class="table-header">
          <h3>Dernières Commandes</h3>
          <router-link to="/admin/orders" class="view-all">Voir tout <ArrowUpRight :size="16" /></router-link>
        </div>
        
        <div v-if="loading" class="loader-container">
          <div class="loader"></div>
        </div>
        
        <div v-else class="table-wrapper">
          <table v-if="orders.length > 0">
            <thead>
              <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Date</th>
                <th>Total</th>
                <th>Statut</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in orders.slice(0, 5)" :key="order.id">
                <td>#{{ order.id }}</td>
                <td>{{ order.user?.name || 'Client Inconnu' }}</td>
                <td>{{ new Date(order.created_at).toLocaleDateString() }}</td>
                <td>{{ parseFloat(order.total_price || 0).toFixed(2) }} €</td>
                <td>
                  <span class="badge" :class="getStatusBadgeClass(order.status)">
                    {{ order.status || 'En attente' }}
                  </span>
                </td>
                <td>
                  <button class="btn-text">Détails</button>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-else class="empty-table">
            <p>Aucune commande récente.</p>
          </div>
        </div>
      </div>

      <div class="side-panel">
        <div class="card admin-menu">
          <h3>Gestion</h3>
          <router-link to="/admin/products" class="menu-item">
            <Package :size="20" /> Produits
          </router-link>
          <router-link to="/admin/categories" class="menu-item">
            <Calendar :size="20" /> Catégories
          </router-link>
          <router-link to="/admin/users" class="menu-item">
            <Users :size="20" /> Utilisateurs
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.admin-dashboard {
  padding-bottom: 4rem;
}

.dashboard-header {
  margin-bottom: 2.5rem;
}

.dashboard-header h1 {
  font-size: 2.5rem;
}

.dashboard-header p {
  color: var(--text-muted);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.stat-card {
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1.25rem;
  position: relative;
}

.stat-icon {
  width: 50px;
  height: 50px;
  border-radius: var(--radius);
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon.orders { background: #e0f2fe; color: #0284c7; }
.stat-icon.revenue { background: #dcfce7; color: #16a34a; }
.stat-icon.products { background: #fef9c3; color: #ca8a04; }
.stat-icon.users { background: #f3e8ff; color: #9333ea; }

.stat-info p {
  font-size: 0.875rem;
  color: var(--text-muted);
  margin-bottom: 0.25rem;
}

.stat-info h3 {
  font-size: 1.5rem;
  margin: 0;
}

.stat-trend {
  position: absolute;
  top: 1rem;
  right: 1rem;
  font-size: 0.75rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.stat-trend.positive { color: var(--success); }

.dashboard-content {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 2rem;
}

.table-card {
  padding: 1.5rem;
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.view-all {
  font-size: 0.875rem;
  color: var(--primary);
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.25rem;
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

.btn-text {
  color: var(--primary);
  font-weight: 600;
}

.admin-menu {
  padding: 1.5rem;
}

.admin-menu h3 {
  margin-bottom: 1.5rem;
  font-size: 1.125rem;
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  border-radius: var(--radius);
  color: var(--text-muted);
  font-weight: 500;
  transition: var(--transition);
}

.menu-item:hover {
  background: var(--bg-color);
  color: var(--primary);
}

.menu-item.router-link-active {
  background: var(--primary-light);
  color: var(--primary-dark);
}

.badge-info { background: #e0f2fe; color: #0369a1; }
.badge-warning { background: #fef3c7; color: #92400e; }

.loader-container {
  display: flex;
  justify-content: center;
  padding: 3rem 0;
}

@media (max-width: 992px) {
  .dashboard-content {
    grid-template-columns: 1fr;
  }
}
</style>
