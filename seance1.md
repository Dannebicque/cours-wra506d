# ApiPlatform

## Présentation

### ApiPlatform

ApiPlatform est un framework qui permet de créer des API REST en quelques lignes de code. Il est basé sur Symfony. Il peut être utilisé seul, ou en complement sur une application Symfony existante.
On parle d'API REST car c'est un standard qui permet de communiquer entre des applications différentes. Il est basé sur le protocole HTTP et utilise les méthodes GET, POST, PUT, DELETE, etc. pour récupérer, créer, modifier et supprimer des données.
ApiPlatform va permettre d'exposer vos entités en tant que ressources, et de créer des routes pour les différentes méthodes HTTP. Il intégre une documentation permettant de tester les routes directement depuis le navigateur.
Il est ensuite possible de tout paramètrer pour configurer des filtres, des routes spécifiques, la sécurité, définir précisement les données accessibles, etc.
La documentation officielle est très complète et permet de bien comprendre le fonctionnement d'ApiPlatform. : <https://api-platform.com/>.
ApiPlatform propose de nombreux formats de sortie, dont JSON-LD, HAL, JSONAPI, etc. Il est possible de configurer les formats de sortie par défaut, et de définir des formats de sortie spécifiques pour chaque ressource.

### RestFull vs GraphQL

## Installation

### Prérequis

ApiPlatform peut s'installer sur un projet Symfony existant, ou sur un projet vierge. Notamment avec une image Docker donnée par ApiPlatform. Pour ce projet, nous allons partir de notre projet Symfony déjà installé.

### Un projet de tests

Afin de le tester nous allons créer trois entités : Movie, Actor et Category.

```bash
php bin/console make:entity
```

Pour chacune des entités, nous allons créer les champs suivants :

- Movie : id, title, description, releaseDate, duration, category
- Actor : id, firstName, lastName
- Category : id, name

Pour tester tous les cas, nous allons créer des relations entre les entités :

- Movie : ManyToOne avec Category
- Movie : ManyToMany avec Actor

### Installation et configuration

Pour installer ApiPlatform, il faut d'ajouter le bundle :

```bash
composer require api
```

A ce stade, vous pouvez lancer votre projet et vérifier que tout fonctionne bien. Pour cela rendez vous sur l'url : <http://localhost:8000/api> /!\ Evidemment vous devez adapter l'url en fonction de votre configuration. Il faut ajouter /api à la fin de l'url de base de votre projet.

A ce stade aucune de nos entités n'est exposée. Il faut donc pour chacune préciser si elle est exposée, et éventuellement configurer précisément comment elle est exposée (les champs, les droits, ...).

Pour exposer une entité, il faut ajouter l'attribut #[ApiResource()] au dessus de la classe. Par exemple :

```php
<?php

namespace App\Entity;

...
use ApiPlatform\Metadata\ApiResource;
...

#[ORM\Entity(repositoryClass: MovieRepository::class)]
#[ApiResource()]
class Movie
{
 ...
}
```

C'est la configuration minimale. Si vous actualisez votre page, vous devriez voir apparaître l'entité Movie dans la liste des ressources exposées.

Sur cette page vous avez toutes les possibilités offertes par ApiPlatform. Vous pouvez tester les différentes routes, et voir les données retournées. Par défaut vous avez les actions suivantes :
- GET /fournisseurs : récupère la liste des fournisseurs
- POST /fournisseurs : crée un nouveau fournisseur (implique d'envoyer les données associées)
- GET /fournisseurs/{id} : récupère un fournisseur en fonction de son id
- PUT /fournisseurs/{id} : remplace un fournisseur en fonction de son id (implique d'envoyer les données associées) par les nouvelles données
- DELETE /fournisseurs/{id} : supprime un fournisseur en fonction de son id
- PATCH /fournisseurs/{id} : modifie un fournisseur en fonction de son id (implique d'envoyer les données associées)

### Premiers essais

Ajoutez des données, publiez les autres entités et testez les réponses d'ApiPlatform. Vous pouvez aussi tester les routes avec Postman.

### Quelques mots sur les données

Source : <https://symfonycasts.com/screencast/api-platform/json-ld>

ApiPlatform utilise de nombreux standards pour exposer les données et pour leur donner du "sens" (dans un contexte de Web sémantique). Par exemple, les données sont exposées en Json-ld. Json-ld est un format de données qui permet de donner du sens aux données. Par exemple, si vous avez un champ "category" qui est une relation vers une autre entité, le format Json-ld va permettre de préciser que c'est une relation, et de donner le lien vers la ressource associée. C'est un format qui est très utilisé dans le monde du Web sémantique.

Le format JSON LD est un format respectant le modèle de données RDF  (Ressource Description Framework) (<https://fr.wikipedia.org/wiki/Resource_Description_Framework>).

  "Resource Description Framework (RDF) est un modèle de graphe destiné à décrire formellement les ressources Web et leurs métadonnées, afin de permettre le traitement automatique de telles descriptions. Développé par le W3C, RDF est le langage de base du Web sémantique."

Le format json-ld (application/ld+json) est un format Json classique, mais des données de contexte sont ajoutées. Notamment un identifiant unique (@id), un contexte (@context), un type (@type), ...

En complet de ce format, ApiPlatform utilise le format Hydra (https://symfonycasts.com/screencast/api-platform/hydra). Hydra est un format de données qui permet de décrire des API REST. Il permet de décrire les différentes routes, les paramètres, les méthodes, etc. Il permet de décrire les données, les relations, etc. Il permet de décrire les erreurs, les codes de retour, etc. Hydra est un standard du Web sémantique.

### Opérations

Par défaut, ApiPlatform propose toujours les 6 opérations définies précédemment. Il est possible de les désactivier, de les configurer, de modifier le comportement par défaut, ou encore de créer des opérations personnalisées.

Une grande partie de la configuration se fait dans l'attribut ApiResource. Par exemple, par défaut pour avoir les 6 opérations on peut écrire :

```php
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;

#[ApiResource(
    description: 'A movie with actors.',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Patch(),
        new Delete(),
    ]
)]
class Movie
{
    ...
}
```

Pour désactiver une opération, il suffit de ne pas l'ajouter dans la liste. Par exemple, pour désactiver la route GET :

```php
#[ApiResource(
    description: 'A movie with actors.',
    operations: [
        new GetCollection(),
        new Post(),
        new Put(),
        new Patch(),
        new Delete(),
    ]
)]
class Movie
{
    ...
}
```

Pour modifier le comportement par défaut, il faut ajouter des paramètres dans l'opération. Par exemple, pour modifier le comportement de la route GET (exemple ici pour modifier l'URL) :

```php
#[ApiResource(
    description: 'A movie with actors.',
    operations: [
        new Get(uriTemplate: '/movie/{id}'),
        new GetCollection(),
        new Post(),
        new Put(),
        new Patch(),
        new Delete(),
    ]
)]
class Movie
{
    ...
}
```

### Contrôler les données exposées

Source : https://symfonycasts.com/screencast/api-platform/serialization-groups

### Gestion des relations

Source : https://symfonycasts.com/screencast/api-platform/relations

#### Exercices

* Configurez les relations entre les entités Movie, Actor et Category. Testez les routes et vérifiez les données retournées. Un film doit permettre de récupérer toutes les informations d'un film avec la liste des acteurs et la catégorie.
* Un acteur doit permettre de récupérer tous les films dans lesquels il a joué.
* Une catégorie doit permettre de récupérer tous les films de cette catégorie.

### Bilan Partie 1

* Ajoutez une entité "Nationalité", générez des données.
* Un acteur à une nationalité, modifiez les éléments en conséquences.
* Lors je récupère un acteur, je veux afficher sa nationalité.
* Gérez l'ensemble des ces modifications avec le git flow.
* Proposez "la requête" permettant d'ajouter un acteur et sa nationalité (faire un fichier md dans votre repository avec la solution)

### Validation des données

Source : https://symfonycasts.com/screencast/api-platform/validation

#### Exercices

* Ajoutez des contraintes de validation sur les entités Movie, Actor et Category. Testez les routes et vérifiez les données retournées.
* Testez avec des données erronées pour contrôler que les contraintes sont bien appliquées.

### Filtres & pagination

Source : https://symfonycasts.com/screencast/api-platform/filters

#### Exercices

* Mettre en places des filtre sur la partie movie (recherche par titre, durée,...)

### Sécurité

#### Rappel de la sécurité dans Symfony

https://cours.davidannebicque.fr/symfony/semestre-3/securite

#### Notion de Voters

https://symfony.com/doc/current/security/voters.html

#### Sécurité dans ApiPlatform

Source : https://symfonycasts.com/screencast/api-platform-security
et
https://api-platform.com/docs/core/security/

#### Exercices

* Créer la partie sécurité et mettre en place les fixtures associées (5 Users et un admin)
* Chaque film aura un "User" associé (l'auteur de la fiche)
* Interdire les fonction PUT/PATCH si on est pas l'auteur
* Interdire la suppression si on est pas admin (un USER sera donc Admin)
* Les catégories ne seront gérables que par l'Admin (PUT, NEW, DELETE)

## Activer GraphQL

Ressources ici : <https://api-platform.com/docs/core/graphql/>

```bash
composer require webonyx/graphql-php
```

Et pour tester : <http://localhost/api/graphql>
