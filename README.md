# TechReconditionné

Site de produits high-tech reconditionnés. Il n'y a pas de vente en ligne. Je l'ai fait avec Symfony 8.1 pour m'entraîner à la méthode.

Technos : PHP 8.5, Symfony, Doctrine, MySQL, Twig, Bootstrap 5, PHPUnit et GitHub Actions.

## Ce que fait le site

Un visiteur peut voir l'accueil (présentation et 3 derniers produits), la liste des produits, la fiche d'un produit, la liste des catégories et les produits d'une catégorie.

Un administrateur connecté peut ajouter, modifier et supprimer des produits et des catégories. Dans le formulaire d'un produit, la catégorie se choisit dans une liste (qui vient de la base) et l'état est limité à bon, moyen ou mauvais. Son prénom s'affiche en haut à droite.

La page /login n'a pas de lien dans le menu, il faut taper l'adresse. Après la connexion on arrive sur la liste des produits, après la déconnexion sur l'accueil.


## Les données

Trois entités : User, Category et Product. Un produit a une seule catégorie et une catégorie a plusieurs produits (ManyToOne). J'ai aussi mis le côté inverse dans Category pour afficher ses produits facilement.
Le sujet indique une cardinalité 1-1, que j'ai interprétée comme (1,1) côté produit.

## Quelques choix

La date d'ajout se remplit toute seule dans le constructeur de Product.
Un nouveau compte n'a que ROLE_USER. 
Je donne ROLE_ADMIN à la main dans la base, comme ça personne ne peut se mettre administrateur en s'inscrivant.
La suppression passe par un formulaire en POST avec un jeton CSRF. On ne peut pas supprimer une catégorie qui a encore des produits.
J'ai ajouté la validation (NotBlank, Positive, Choice, Length) après coup : un de mes tests a montré qu'un champ vide faisait une erreur 500.

## Les tests

Il y a 30 tests PHPUnit : 5 unitaires (sur Product et Category) et 25 fonctionnels (pages publiques, connexion et rôles, CRUD du backoffice, refus des mauvaises données).

Ils utilisent une base à part, reconditionne_test, pour ne pas toucher à mes vrais produits. Il faut la créer une fois, avec un fichier .env.test.local
Le fichier .github/workflows/tests.yml lance ces tests tout seul sur GitHub à chaque pull request.

## Ce qui manque

Pas d'images pour les produits.

## Mes galères

Un mauvais use pour Route (Annotation au lieu de Attribute). Mes pages n'existaient pas et rien ne le disait.
Un endif mal placé dans le menu : l'administrateur ne voyait plus le contenu des pages.
Mes tests ne lisaient pas mon .env.local. Il a fallu créer .env.test.local.
assertSelectorTextContains ne regarde que le premier élément trouvé, donc pour vérifier une liste j'ai utilisé body ou assertSelectorCount.


