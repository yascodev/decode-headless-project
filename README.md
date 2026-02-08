# Decode Headless Plugin - Projet Semestriel

Ce plugin WordPress a été développé dans le cadre du projet final pour la communication avec un CMS Headless. Il utilise une architecture **Orientée Objet (POO)** et des interactions **asynchrones (AJAX)**.

## 🚀 Fonctionnalités implémentées

### 1. Administration & Sécurité
- **Interface dédiée** : Un menu "Headless Admin" intégré à la barre latérale WordPress.
- **Connexion Asynchrone** : Formulaire de connexion (Login/Password/Secret Key) traitant les données via AJAX sans rechargement de page.
- **Gestion de Session** : Génération d'un token de sécurité stocké en base de données après authentification réussie.
- **Sécurité** : Utilisation de **Nonces WordPress** pour protéger toutes les requêtes AJAX.

### 2. Consultation du Contenu (API)
- **Récupération de données** : Intégration de l'API WordPress (`wp_remote_get`) pour récupérer des articles depuis un CMS distant (simulé via JSONPlaceholder).
- **Affichage dynamique** : Tableau de bord listant les contenus reçus directement dans l'interface d'administration.

### 3. Shortcodes (Affichage Client)
Trois shortcodes sont disponibles pour intégrer le contenu Headless dans les pages :
- `[headless_post id="X"]` : Affiche un article spécifique par son ID.
- `[headless_list]` : Affiche une liste des derniers articles.
- `[headless_info]` : Affiche les informations de statut du CMS.

### 4. Optimisation & Cache (Point Bonus)
- **Transients API** : Mise en cache des réponses de l'API pendant 1 heure pour réduire la charge serveur et améliorer la vitesse de chargement.
- **Gestion du cache** : Bouton "Vider le cache" disponible dans l'admin pour forcer une nouvelle synchronisation.

## 🛠️ Installation

1. Déposez le dossier `decode-headless-plugin` dans le répertoire `/wp-content/plugins/` de votre installation WordPress.
2. Activez le plugin via l'onglet **Extensions** du tableau de bord.
3. Accédez au menu **Headless Admin** pour configurer la connexion.

## 💻 Technologies utilisées
- **PHP** (Architecture Class-based POO)
- **JavaScript / jQuery** (AJAX)
- **WordPress API** (Transients, HTTP API, Shortcodes)
- **CSS** (Styles natifs WP Admin)

---
*Projet réalisé par Yaniss LAMBEAU*