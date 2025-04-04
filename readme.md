# JamSpace

## Introduction

JamSpace est une application web permettant aux musiciens de se connecter et d'échanger.

## Stack technique Front-end

- [Vite](https://vite.dev/)
- [React](https://react.dev/)
- [TypeScript](https://www.typescriptlang.org/)
- [TailwindCSS](https://tailwindcss.com/)
- [Shadcn/ui](https://ui.shadcn.com/)
- [pnpm](https://pnpm.io/)
- [JWT](https://en.wikipedia.org/wiki/JSON_Web_Token)

## Stack technique Back-end

- [PHP](https://www.php.net/)
- [PDO](https://www.php.net/manual/fr/book.pdo.php)
- [sqlite](https://www.sqlite.org/)
- [JWT](https://github.com/firebase/php-jwt)
- [Composer](https://getcomposer.org/)

## Fonctionnalités

### Authentification

#### Routes protégées

Certaines routes de l'API sont protégées; elles nécessitent au client d'envoyer leur
`jsonwebtoken`, qui est validé par le serveur. Les routes protégées sont les routes sensibles, comme par exemple la modification d'un utilisateur. Prière se référer à la description de l'API disponible dans le fichier `openapi.yaml`

#### Use-cases

1. L'utilisateur crée son compte.
2. L'utilisateur se connecte à l'application avec ses identifiants.
3. L'utilisateur se déconnecte de l'application.

#### Implémentation

1. Création de compte

- l'utilisateur entre ses identifiants sur l'application
- le côté client de l'application envoie une requête au côté serveur
- le serveur PHP reçoit la requête et crée une row dans la table `users`
- le serveur génère un `jsonwebtoken` et répond à la requête avec
- le client reçoit le `jsonwebtoken` et le stocke dans le `localstorage`
- l'utilisateur a créé un compte et est connecté à l'application

2. Connexion à l'application

- l'utilisateur entre ses identifiants sur l'application
- le côté client de l'application envoie une requête au côté serveur
- le serveur PHP reçoit la requête et vérifie les identifiants.
- le serveur génère un `jsonwebtoken` et répond à la requête avec
- le client reçoit le `jsonwebtoken` et le stocke dans le `localstorage`
- l'utilisateur est connecté à l'application

3. déconnexion de l'application

- le client supprime le `jsonwebtoken` du `localStorage`
- l'utilisateur est déconnecté

#### Hachage des mots de passe

Les mots de passe utilisateur sont stockés dans la base de données sous forme hachée.

### Posts

#### Use-cases

1. L'utilisateur veut accéder aux posts de la communauté

#### Implémentation

1. Accès aux posts de la communauté

- le client envoie une requête au serveur
- le serveur trouve tous les posts dans la base de données
- il répond à la requête avec ces posts
- le client affiche tous les posts sur la page "Home" de l'application

### Architecture Frontend

#### Gestion de l'authentification

L'application utilise un système de Context/Provider pour la gestion de l'authentification:

- Un `AuthContext` global est implémenté pour partager l'état d'authentification à travers l'application
- Le `AuthProvider` encapsule la logique d'authentification et expose:
  - L'état de connexion de l'utilisateur
  - Les méthodes de connexion, déconnexion et inscription
  - La gestion du token JWT
- Cette approche évite le "prop drilling" et centralise la logique d'authentification

#### Interface utilisateur

- **Shadcn/ui** est utilisé pour les composants UI, offrant:

  - Des composants accessibles et réutilisables
  - Une interface cohérente et moderne
  - Une intégration parfaite avec Tailwind CSS
  - Des composants personnalisables selon les besoins du design

- **Tailwind CSS** est utilisé pour le styling:
  - Approche utility-first qui accélère le développement
  - Styles directement appliqués dans les composants
  - Design responsive facilité
  - Personnalisation facile via le fichier de configuration
  - Optimisation automatique pour la production
