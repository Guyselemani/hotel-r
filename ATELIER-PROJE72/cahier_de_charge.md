# Cahier des Charges - Système de Gestion Hôtelière "Hotel Royal"

## 1. Présentation du Projet

### 1.1 Contexte
Le projet consiste en la création d'un système complet de gestion hôtelière pour l'Hotel Royal, incluant un site web public et une interface d'administration pour la gestion des opérations quotidiennes.

### 1.2 Objectifs
- Fournir une plateforme de réservation en ligne intuitive
- Permettre la gestion complète des chambres et réservations
- Offrir une expérience utilisateur optimale
- Assurer la sécurité et la fiabilité du système

## 2. Analyse Fonctionnelle

### 2.1 Fonctionnalités du Site Public

#### 2.1.1 Page d'accueil (index.php)
- **Section Héros** : Présentation de l'hôtel avec call-to-action
- **Section Présentation** : Valeurs et esprit de l'établissement
- **Section Services** : Description des services (chambres, restaurant, spa, parking, navette)
- **Section Pourquoi choisir** : Avantages compétitifs
- **Section Chambres** : Aperçu des types de chambres disponibles
- **Section FAQ** : Informations pratiques (arrivée/départ, enfants, paiement, langues)
- **Section CTA** : Boutons d'action pour réservation et services

#### 2.1.2 Page Réservation (reservation.php)
- Formulaire de réservation avec champs :
  - Nom complet
  - Email
  - Téléphone
  - Type de chambre (liste déroulante)
  - Date d'arrivée
  - Date de départ
  - Message/demande spéciale
- Validation côté client et serveur
- Traitement via traitement.php

#### 2.1.3 Page Chambres (chambres.php)
- Affichage des types de chambres disponibles
- Tarifs et descriptions détaillées
- Galerie d'images

#### 2.1.4 Page Services (services.php)
- Description détaillée des services
- Tarifs et conditions
- Possibilité de réservation

#### 2.1.5 Page Contact (contact.php)
- Formulaire de contact
- Coordonnées de l'hôtel
- Plan d'accès

### 2.2 Fonctionnalités d'Administration

#### 2.2.1 Gestion des Chambres (admin_chambres.php)
- **Ajout de chambres** :
  - Numéro de chambre
  - Étage
  - Type de chambre
  - Statut (DISPONIBLE, OCCUPEE, MAINTENANCE, NETTOYAGE, HORS_SERVICE)
- **Modification de chambres** : Édition des informations existantes
- **Suppression de chambres** : Avec confirmation
- **Changement de statut** : Mise à jour rapide du statut
- **Liste des chambres** : Tableau avec toutes les informations

#### 2.2.2 Gestion des Réservations
- Consultation des réservations
- Modification des réservations
- Annulation de réservations
- Gestion des arrivées/départs

#### 2.2.3 Gestion des Clients
- Base de données clients
- Historique des séjours
- Informations de contact

## 3. Architecture Technique

### 3.1 Technologies Utilisées
- **Frontend** : HTML5, CSS3, JavaScript
- **Backend** : PHP 7.4+
- **Base de données** : MySQL 8.0+
- **Serveur web** : Apache/Nginx
- **Framework CSS** : Styles personnalisés avec variables CSS

### 3.2 Structure des Fichiers
```
/projet/
├── index.php              # Page d'accueil
├── reservation.php        # Formulaire de réservation
├── chambres.php           # Présentation des chambres
├── services.php           # Description des services
├── contact.php            # Page de contact
├── admin_chambres.php     # Administration des chambres
├── traitement.php         # Traitement des réservations
├── config.php             # Configuration base de données
├── header.php             # En-tête du site
├── footer.php             # Pied de page
├── style.css              # Feuille de style principale
├── styles2.css            # Styles supplémentaires
├── database.sql           # Script de création de la BDD
├── insert_sample.php      # Données d'exemple
└── IMG/                   # Images du site
```

### 3.3 Base de Données

#### 3.3.1 Tables Principales
- **clients** : Informations clients
- **types_chambres** : Types de chambres disponibles
- **chambres** : Chambres physiques
- **reservations** : Réservations clients
- **reservations_chambres** : Liaison réservations-chambres
- **personnel** : Équipe hôtelière
- **roles** : Rôles du personnel
- **factures** : Facturation
- **services** : Services additionnels

#### 3.3.2 Fonctionnalités Base de Données
- Gestion des tarifs saisonniers
- Calcul automatique des prix
- Gestion des taxes
- Historique des séjours

## 4. Interface Utilisateur

### 4.1 Design
- **Thème** : Moderne, élégant, professionnel
- **Couleurs** : Palette bleu/cyan (#00d4ff) avec accents (#ff006e)
- **Typographie** : Orbitron pour titres, Roboto pour contenu
- **Mode sombre** : Implémenté avec variables CSS

### 4.2 Responsive Design
- Adaptation mobile, tablette, desktop
- Breakpoints adaptés
- Navigation optimisée pour mobile

### 4.3 Accessibilité
- Contraste des couleurs suffisant
- Navigation au clavier
- Labels appropriés pour les formulaires
- Texte alternatif pour les images

## 5. Sécurité

### 5.1 Authentification
- Système de connexion pour l'administration
- Sessions sécurisées
- Protection contre les attaques CSRF

### 5.2 Validation des Données
- Sanitisation des entrées utilisateur
- Validation côté client et serveur
- Protection contre les injections SQL (PDO)
- Échappement des caractères spéciaux

### 5.3 Sécurité des Données
- Chiffrement des mots de passe
- Protection des données sensibles
- Sauvegarde régulière de la base de données

## 6. Performances

### 6.1 Optimisation
- Compression des images
- Minification CSS/JS
- Cache des ressources statiques
- Optimisation des requêtes SQL

### 6.2 Disponibilité
- Temps de réponse < 2 secondes
- Uptime > 99%
- Gestion des erreurs élégante

## 7. Maintenance et Évolution

### 7.1 Mises à Jour
- Système de versioning
- Documentation technique
- Procédures de déploiement

### 7.2 Support
- Logs d'erreurs détaillés
- Monitoring des performances
- Sauvegarde automatique

## 8. Contraintes et Risques

### 8.1 Contraintes Techniques
- Compatibilité navigateurs (Chrome, Firefox, Safari, Edge)
- Performance sur connexions lentes
- Accessibilité WCAG 2.1 niveau AA

### 8.2 Risques Identifiés
- Perte de données
- Indisponibilité du service
- Sécurité des paiements (si intégration future)
- Évolution des réglementations RGPD

## 9. Livrables

### 9.1 Code Source
- Application web complète
- Scripts SQL de base de données
- Documentation technique
- Guide d'installation

### 9.2 Documentation
- Manuel utilisateur
- Guide développeur
- Cahier de tests

## 10. Planning Prévisionnel

### Phase 1 : Analyse et Conception (1 semaine)
- Analyse des besoins détaillée
- Conception de l'architecture
- Maquettage des interfaces

### Phase 2 : Développement Frontend (2 semaines)
- Création des pages publiques
- Intégration du design
- Responsive design

### Phase 3 : Développement Backend (3 semaines)
- Implémentation de la logique métier
- Création de l'administration
- Intégration base de données

### Phase 4 : Tests et Déploiement (1 semaine)
- Tests fonctionnels
- Tests de sécurité
- Déploiement en production

### Phase 5 : Formation et Maintenance (Continue)
- Formation des utilisateurs
- Support technique
- Mises à jour évolutives

## 11. Budget Estimatif

### Coûts de Développement
- Analyse et conception : 15%
- Développement frontend : 25%
- Développement backend : 35%
- Tests et déploiement : 15%
- Documentation : 10%

### Coûts d'Infrastructure
- Serveur dédié : Selon besoins
- Domaine et SSL : ~50€/an
- Maintenance : 20% du coût initial/an

## 12. Critères d'Acceptation

### Fonctionnels
- [ ] Toutes les pages s'affichent correctement
- [ ] Formulaire de réservation fonctionnel
- [ ] Interface d'administration opérationnelle
- [ ] Base de données correctement structurée

### Techniques
- [ ] Temps de chargement < 3 secondes
- [ ] Compatible tous navigateurs modernes
- [ ] Code sécurisé et optimisé
- [ ] Responsive sur tous appareils

### Qualité
- [ ] Code documenté et commenté
- [ ] Tests unitaires passés
- [ ] Validation W3C conforme
- [ ] Accessibilité respectée

---

**Date de création** : Décembre 2024
**Version** : 1.0
**Auteur** : Équipe de développement
**Validation** : En attente
