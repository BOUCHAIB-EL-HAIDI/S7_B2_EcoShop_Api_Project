# EcoShop Frontend 🌿

Une application Single Page (SPA) moderne construite avec Vue.js 3, Pinia et Vite. Elle consomme l'API REST EcoShop pour offrir une expérience de shopping éco-responsable fluide et premium.

## 🚀 Fonctionnalités

### Authentification & Profil
- Inscription et Connexion avec gestion des tokens Sanctum.
- Persistance du token dans le `localStorage`.
- Navigation guards pour protéger les routes.
- Page de profil utilisateur avec données en temps réel.

### Catalogue Produits
- Liste paginée des produits avec filtrage par catégorie.
- Recherche dynamique par mot de passe.
- Fiche produit détaillée avec indicateurs de stock.
- Design responsive pour mobile et desktop.

### Panier & Commandes
- Gestion complète du panier avec Pinia (Ajout, Modification, Suppression).
- Calcul automatique des sous-totaux et totaux.
- Badge dynamique sur l'icône du panier.
- Processus de checkout avec récapitulatif de commande.
- Vidage automatique du panier après commande réussie.

### Interface Administrateur
- Dashboard avec statistiques et dernières commandes.
- CRUD complet des produits (Création, Lecture, Modification, Suppression).
- Gestion des catégories.
- Upload d'images pour les produits.
- Routes protégées par rôle (Admin uniquement).

## 🛠️ Stack Technique

- **Framework**: Vue.js 3 (Composition API `<script setup>`)
- **State Management**: Pinia
- **Routing**: Vue Router
- **HTTP Client**: Axios (avec interceptors centralisés)
- **Icons**: Lucide Vue Next
- **Styles**: Vanilla CSS (Modern design system)

## 📦 Installation

1. **Cloner le repository**
   ```bash
   git clone <repo_url>
   cd S7_B2_EcoShop_Api_Project/frontend
   ```

2. **Installer les dépendances**
   ```bash
   npm install
   ```

3. **Configuration de l'environnement**
   Copiez le fichier `.env.example` vers `.env` et ajustez l'URL de votre API :
   ```bash
   VITE_API_URL=http://localhost:8000/api
   ```

4. **Lancer l'application en développement**
   ```bash
   npm run dev
   ```

## 🌐 Configuration CORS / Proxy

Si vous rencontrez des problèmes de CORS pendant le développement, vous pouvez configurer un proxy dans `vite.config.js`. Par défaut, l'application est configurée pour envoyer les credentials (cookies) nécessaires à Laravel Sanctum.

```javascript
// vite.config.js
export default defineConfig({
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      }
    }
  }
})
```

## 📂 Structure du Projet

```
frontend/
├── src/
│   ├── api/          # (Optionnel) Endpoints spécifiques
│   ├── components/   # Composants réutilisables
│   ├── views/        # Pages de l'application
│   ├── stores/       # Pinia stores (auth, cart)
│   ├── services/     # Axios client centralisé
│   ├── router/       # Configuration Vue Router
│   ├── styles/       # Design system CSS
│   └── App.vue       # Composant racine
├── .env              # Variables d'environnement
└── package.json      # Dépendances
```
