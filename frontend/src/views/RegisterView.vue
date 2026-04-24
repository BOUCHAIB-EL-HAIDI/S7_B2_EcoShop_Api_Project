<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { UserPlus, Mail, Lock, User, AlertCircle } from 'lucide-vue-next';

const auth = useAuthStore();
const router = useRouter();

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
});

const handleSubmit = async () => {
  try {
    await auth.register(form.value);
    router.push({ name: 'home' });
  } catch (err) {
    // Error handled in store
  }
};
</script>

<template>
  <div class="auth-page">
    <div class="auth-card card animate-fade-in">
      <div class="auth-header">
        <div class="auth-icon">
          <UserPlus :size="32" />
        </div>
        <h1>Créer un compte</h1>
        <p>Rejoignez notre communauté éco-responsable.</p>
      </div>

      <form @submit.prevent="handleSubmit" class="auth-form">
        <div v-if="auth.error" class="error-alert">
          <AlertCircle :size="18" />
          <span>{{ auth.error }}</span>
        </div>

        <div class="form-group">
          <label for="name">Nom complet</label>
          <div class="input-wrapper">
            <User :size="18" class="input-icon" />
            <input 
              id="name"
              type="text" 
              v-model="form.name" 
              placeholder="Jean Dupont" 
              required
            />
          </div>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <div class="input-wrapper">
            <Mail :size="18" class="input-icon" />
            <input 
              id="email"
              type="email" 
              v-model="form.email" 
              placeholder="votre@email.com" 
              required
            />
          </div>
        </div>

        <div class="form-group">
          <label for="password">Mot de passe</label>
          <div class="input-wrapper">
            <Lock :size="18" class="input-icon" />
            <input 
              id="password"
              type="password" 
              v-model="form.password" 
              placeholder="••••••••" 
              required
            />
          </div>
        </div>

        <div class="form-group">
          <label for="password_confirmation">Confirmer le mot de passe</label>
          <div class="input-wrapper">
            <Lock :size="18" class="input-icon" />
            <input 
              id="password_confirmation"
              type="password" 
              v-model="form.password_confirmation" 
              placeholder="••••••••" 
              required
            />
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-full" :disabled="auth.loading">
          <span v-if="auth.loading" class="btn-loader"></span>
          <span v-else>S'inscrire</span>
        </button>

        <p class="auth-footer">
          Déjà un compte ? 
          <router-link to="/login">Se connecter</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<style scoped>
.auth-page {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 80vh;
  padding: 2rem;
}

.auth-card {
  width: 100%;
  max-width: 450px;
  padding: 3rem;
}

.auth-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.auth-icon {
  width: 64px;
  height: 64px;
  background: var(--primary-light);
  color: var(--primary);
  border-radius: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
}

.auth-header h1 {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.auth-header p {
  color: var(--text-muted);
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-main);
}

.input-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
}

.input-wrapper input {
  width: 100%;
  padding-left: 3rem;
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
  font-weight: 500;
}

.btn-full {
  width: 100%;
  height: 3rem;
}

.auth-footer {
  text-align: center;
  font-size: 0.875rem;
  color: var(--text-muted);
  margin-top: 1rem;
}

.auth-footer a {
  color: var(--primary);
  font-weight: 600;
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
</style>
