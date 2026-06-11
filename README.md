# SlahPC

## Description du projet

**SlahPC** est un site web de réparation informatique au Maroc. Le projet permet aux utilisateurs de demander des services de réparation pour leurs ordinateurs, que ce soit pour des problèmes matériels, logiciels, sauvegarde de données ou suppression de virus et malwares.

Le site contient une partie utilisateur pour consulter les services et envoyer des demandes, ainsi qu’une partie administrateur pour gérer les utilisateurs, les services, les demandes de réparation et les messages reçus.

## Objectif du projet

L’objectif principal de SlahPC est de faciliter la communication entre les clients et le réparateur informatique. Le client peut envoyer une demande en ligne, expliquer son problème et suivre l’état de sa demande.

Ce projet a été réalisé dans le cadre du Projet de Fin d’Études afin de mettre en pratique les compétences acquises en développement web, base de données, conception UML et gestion de projet.

## Fonctionnalités principales

### Côté utilisateur

- Création d’un compte utilisateur
- Connexion et déconnexion
- Consultation des services proposés
- Envoi d’une demande de réparation
- Contact via un formulaire
- Consultation des informations du site

### Côté administrateur

- Connexion à un espace administrateur
- Gestion des utilisateurs
- Gestion des services
- Consultation des demandes de réparation
- Modification de l’état des demandes
- Consultation des messages envoyés par les clients

## Technologies utilisées

- **HTML5** : structure des pages web
- **CSS3** : design et mise en page
- **JavaScript** : interactions côté client
- **PHP** : traitement côté serveur
- **MySQL** : gestion de la base de données
- **XAMPP** : serveur local Apache et MySQL
- **VS Code** : éditeur de code

## Structure du projet

```text
SlahPC/
│
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
├── includes/
│   ├── config.php
│   ├── db.php
│   └── functions.php
│
├── admin.php
├── dashboard.php
├── index.html
├── login.php
├── register.php
├── contact.php
├── services.php
├── setup.php
└── README.md
```

## Base de données

La base de données du projet contient plusieurs tables principales :

- **users** : stocke les informations des utilisateurs et administrateurs
- **services** : stocke les services proposés par le site
- **requests** : stocke les demandes de réparation envoyées par les clients
- **messages** : stocke les messages du formulaire de contact
- **appointments** : stocke les rendez-vous ou interventions planifiées

## Installation du projet

### 1. Installer XAMPP

Télécharger et installer XAMPP sur l’ordinateur.

### 2. Copier le projet

Copier le dossier du projet dans le dossier suivant :

```text
C:/xampp/htdocs/
```

Exemple :

```text
C:/xampp/htdocs/SlahPC/
```

### 3. Démarrer le serveur

Ouvrir XAMPP Control Panel puis démarrer :

- Apache
- MySQL

### 4. Créer la base de données

Ouvrir phpMyAdmin dans le navigateur :

```text
http://localhost/phpmyadmin
```

Créer une base de données, par exemple :

```text
slahpc_db
```

### 5. Importer le script SQL

Importer le fichier SQL du projet dans la base de données créée.

### 6. Configurer la connexion

Modifier le fichier de connexion à la base de données si nécessaire :

```php
$host = "localhost";
$dbname = "slahpc_db";
$username = "root";
$password = "";
```

### 7. Lancer le site

Ouvrir le navigateur et accéder au projet :

```text
http://localhost/SlahPC/
```

## Utilisation

L’utilisateur peut créer un compte, se connecter, consulter les services disponibles et envoyer une demande de réparation. L’administrateur peut gérer les demandes reçues et modifier leur statut selon l’avancement du traitement.

## Statuts des demandes

Les demandes de réparation peuvent avoir plusieurs statuts :

- En attente
- Acceptée
- En cours
- Terminée
- Annulée

## Sécurité

Le projet prend en compte plusieurs aspects de sécurité :

- Validation des champs de formulaire
- Protection contre les champs vides
- Utilisation de mots de passe sécurisés
- Séparation entre utilisateur simple et administrateur
- Contrôle d’accès à l’espace administrateur

## Améliorations possibles

- Ajouter un système de notification par email
- Ajouter le suivi détaillé des réparations
- Ajouter le paiement en ligne
- Ajouter un système d’avis clients
- Améliorer le tableau de bord administrateur
- Ajouter une version mobile plus avancée

## Auteur

Projet réalisé par **Tarik Bu** dans le cadre du Projet de Fin d’Études.

## Licence

Ce projet est réalisé à des fins éducatives.


