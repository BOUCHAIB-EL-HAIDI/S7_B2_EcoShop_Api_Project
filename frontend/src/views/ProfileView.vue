<script setup>
import { useAuthStore } from '../stores/auth';
import { User, Mail, Shield, Calendar, Package } from 'lucide-vue-next';

const auth = useAuthStore();
</script>

<template>
  <div class="profile-view container">
    <div class="profile-header">
      <h1>Mon Profil</h1>
      <p>Gérez vos informations personnelles et consultez vos activités.</p>
    </div>

    <div class="profile-grid">
      <div class="card profile-info-card animate-fade-in">
        <div class="user-avatar">
          <div class="avatar-circle">
            {{ auth.user?.name?.charAt(0).toUpperCase() }}
          </div>
        </div>
        
        <div class="user-details">
          <div class="detail-row">
            <User :size="20" class="icon" />
            <div>
              <label>Nom complet</label>
              <p>{{ auth.user?.name }}</p>
            </div>
          </div>
          
          <div class="detail-row">
            <Mail :size="20" class="icon" />
            <div>
              <label>Email</label>
              <p>{{ auth.user?.email }}</p>
            </div>
          </div>

          <div class="detail-row">
            <Shield :size="20" class="icon" />
            <div>
              <label>Rôle</label>
              <p class="role-badge">{{ auth.isAdmin ? 'Administrateur' : 'Client' }}</p>
            </div>
          </div>

          <div class="detail-row">
            <Calendar :size="20" class="icon" />
            <div>
              <label>Membre depuis</label>
              <p>{{ new Date(auth.user?.created_at).toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
            </div>
          </div>
        </div>

        <div class="profile-actions">
          <button class="btn btn-outline btn-full">Modifier mes informations</button>
        </div>
      </div>

      <div class="profile-activity">
        <div class="card activity-card">
          <div class="activity-header">
            <Package :size="20" />
            <h3>Mes Commandes Récentes</h3>
          </div>
          <div class="empty-activity">
            <p>Vous n'avez pas encore passé de commande.</p>
            <router-link to="/" class="btn btn-primary">Faire du shopping</router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.profile-view {
  padding-bottom: 5rem;
}

.profile-header {
  margin-bottom: 3rem;
}

.profile-header h1 {
  font-size: 2.5rem;
}

.profile-header p {
  color: var(--text-muted);
}

.profile-grid {
  display: grid;
  grid-template-columns: 400px 1fr;
  gap: 3rem;
  align-items: start;
}

.profile-info-card {
  padding: 3rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: left;
}

.user-avatar {
  margin-bottom: 2.5rem;
}

.avatar-circle {
  width: 100px;
  height: 100px;
  background: var(--primary);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  font-weight: 700;
  box-shadow: 0 0 0 8px var(--primary-light);
}

.user-details {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  margin-bottom: 2.5rem;
}

.detail-row {
  display: flex;
  gap: 1rem;
}

.detail-row .icon {
  color: var(--primary);
  margin-top: 0.25rem;
}

.detail-row label {
  display: block;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.detail-row p {
  font-size: 1.125rem;
  font-weight: 500;
  color: var(--text-main);
}

.role-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  background: var(--primary-light);
  color: var(--primary-dark);
  border-radius: var(--radius-full);
  font-size: 0.875rem !important;
  font-weight: 600 !important;
  margin-top: 0.25rem;
}

.profile-actions {
  width: 100%;
}

.activity-card {
  padding: 2rem;
}

.activity-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 2rem;
  color: var(--primary);
}

.activity-header h3 {
  color: var(--text-main);
}

.empty-activity {
  text-align: center;
  padding: 4rem 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.5rem;
  color: var(--text-muted);
}

@media (max-width: 992px) {
  .profile-grid {
    grid-template-columns: 1fr;
  }
}
</style>
