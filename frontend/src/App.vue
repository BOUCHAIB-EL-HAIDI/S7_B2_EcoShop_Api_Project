<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from './stores/auth';
import { useCartStore } from './stores/cart';
import { ShoppingCart, User, LogOut, LayoutDashboard, Menu, X } from 'lucide-vue-next';
import { ref } from 'vue';

const auth = useAuthStore();
const cart = useCartStore();
const router = useRouter();
const isMenuOpen = ref(false);

onMounted(async () => {
  if (auth.isAuthenticated) {
    await auth.fetchUser();
    await cart.fetchCart();
  }
});

const handleLogout = async () => {
  await auth.logout();
  cart.clearCart();
  router.push({ name: 'login' });
};

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
};
</script>

<template>
  <div class="app-layout">
    <nav class="navbar">
      <div class="container navbar-content">
        <router-link to="/" class="logo">
          <span class="logo-icon">🌿</span>
          <span class="logo-text">EcoShop</span>
        </router-link>

        <div class="nav-links" :class="{ 'is-open': isMenuOpen }">
          <router-link to="/" class="nav-link" @click="isMenuOpen = false">Produits</router-link>
          
          <template v-if="auth.isAuthenticated">
            <router-link v-if="auth.isAdmin" to="/admin" class="nav-link admin-link" @click="isMenuOpen = false">
              <LayoutDashboard :size="18" /> Admin
            </router-link>
            <router-link to="/profile" class="nav-link" @click="isMenuOpen = false">
              <User :size="18" /> Profil
            </router-link>
            <button @click="handleLogout" class="nav-link logout-btn">
              <LogOut :size="18" /> Déconnexion
            </button>
          </template>
          
          <template v-else>
            <router-link to="/login" class="nav-link" @click="isMenuOpen = false">Connexion</router-link>
            <router-link to="/register" class="btn btn-primary" @click="isMenuOpen = false">S'inscrire</router-link>
          </template>
        </div>

        <div class="nav-actions">
          <router-link to="/cart" class="cart-btn">
            <ShoppingCart :size="24" />
            <span v-if="cart.totalItems > 0" class="cart-badge">{{ cart.totalItems }}</span>
          </router-link>
          
          <button class="menu-toggle" @click="toggleMenu">
            <Menu v-if="!isMenuOpen" :size="24" />
            <X v-else :size="24" />
          </button>
        </div>
      </div>
    </nav>

    <main class="main-content">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <footer class="footer">
      <div class="container">
        <p>&copy; 2026 EcoShop - Produits Écologiques et Durables.</p>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.navbar {
  height: 70px;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid var(--border-color);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.navbar-content {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.logo {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-family: 'Outfit', sans-serif;
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--primary);
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 500;
  color: var(--text-muted);
}

.nav-link:hover, .router-link-active {
  color: var(--primary);
}

.admin-link {
  color: var(--secondary);
}

.logout-btn {
  color: var(--danger);
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.cart-btn {
  position: relative;
  color: var(--text-main);
  transition: var(--transition);
}

.cart-btn:hover {
  color: var(--primary);
}

.cart-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: var(--danger);
  color: white;
  font-size: 0.7rem;
  font-weight: 700;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.menu-toggle {
  display: none;
}

.main-content {
  min-height: calc(100vh - 140px);
  padding: 2rem 0;
}

.footer {
  padding: 2rem 0;
  border-top: 1px solid var(--border-color);
  text-align: center;
  color: var(--text-muted);
  font-size: 0.875rem;
}

/* Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@media (max-width: 768px) {
  .menu-toggle {
    display: block;
  }

  .nav-links {
    position: fixed;
    top: 70px;
    left: 0;
    right: 0;
    background: white;
    flex-direction: column;
    padding: 2rem;
    gap: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    transform: translateY(-100%);
    opacity: 0;
    pointer-events: none;
    transition: var(--transition);
  }

  .nav-links.is-open {
    transform: translateY(0);
    opacity: 1;
    pointer-events: all;
  }
}
</style>
