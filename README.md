# Slahpc

Slahpc est une application web de réparation informatique réalisée en PHP et MySQL. Elle permet aux clients de créer un compte, demander une intervention, suivre son statut et laisser un avis après une réparation terminée.

L'administrateur peut consulter les clients et les demandes, définir les prix, modifier les statuts et modérer les avis.

## Technologies

- PHP 8.2
- MySQL / MariaDB
- HTML5
- CSS3
- JavaScript
- PDO avec requêtes préparées

## Structure

```text
Projet-De-Fin-D-tude-PFE-/
├── assets/
│   ├── app.js
│   ├── password-toggle.js
│   ├── register-validation.js
│   ├── styles.css
│   └── images
├── includes/
│   └── app.php
├── admin.php
├── dashboard.php
├── forgot-password.php
├── index.html
├── index.php
├── login.php
├── logout.php
├── register.php
└── setup.php
```

`index.html` contient le modèle statique de la page d'accueil. `index.php` charge ce modèle et injecte les éléments dynamiques liés à la session et aux rendez-vous.

`includes/app.php` centralise la session, la connexion PDO, les traductions, la protection CSRF et les fonctions partagées.

## Installation locale

1. Installer XAMPP avec Apache, MySQL et PHP 8.2 ou une version compatible.
2. Copier le projet dans `C:\xampp\htdocs\Slahpc`.
3. Démarrer Apache et MySQL.
4. Vérifier les paramètres de connexion dans `includes/app.php`.
5. Ouvrir `http://localhost/Slahpc/setup.php`.
6. Créer le premier compte administrateur avec le formulaire sécurisé.
7. Ouvrir `http://localhost/Slahpc/`.

Le script `setup.php` crée la base `slah_pc`, met à niveau les anciennes colonnes et ajoute les services par défaut. Il est volontairement limité aux requêtes provenant de la machine locale.

## Fonctionnalités

### Client

- inscription et connexion sécurisées ;
- demande de rendez-vous selon un service actif ;
- suivi du prix et du statut de chaque réparation ;
- avis disponible uniquement après une réparation terminée ;
- interface en anglais, français et arabe.

### Administration

- tableau de bord avec statistiques ;
- consultation des rendez-vous et des clients ;
- modification contrôlée des statuts ;
- devis manuel pour les réparations matérielles ;
- prix fixe pour les autres services ;
- approbation ou rejet des avis.

## Sécurité

- mots de passe stockés avec `password_hash()` ;
- requêtes PDO préparées ;
- échappement HTML centralisé avec `e()` ;
- protection CSRF sur tous les formulaires POST ;
- régénération de l'identifiant de session après connexion ;
- cookies de session `HttpOnly` et `SameSite=Lax` ;
- contrôle des rôles utilisateur et administrateur ;
- validation serveur des identifiants, statuts, prix et avis.

## Vérification

Vérifier la syntaxe PHP depuis le dossier du projet :

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object {
    php -l $_.FullName
}
```

## Auteur

Projet de Fin d'Études réalisé par Tarik Bufardi.
