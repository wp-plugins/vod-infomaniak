=== Plugin Name ===
Contributors: vod-infomaniak
Tags: video, manage
Requires at least: 2.8.6
Tested up to: 3.2.1

Easily embed and manage videos from Infomaniak VOD in your posts, comments and RSS feeds. You need an Infomaniak VOD account to use this plugin.

== Description ==

Attention : Ce plugin est encore en cours de développement, il est fortement déconseillé de l'utiliser dans un environnement de production.

Ce plugin vous permet de gérer facilement les interactions entre votre espace VOD et votre blog.
Il vous permet en toute simplicité de récupérer et de gérer l'ensemble de vos vidéos. 
Il offre également de nombreuses autres options tels que :

* La mise à jour automatique lors de l'ajout de nouvelles vidéos
* La récupération automatique des players existants
* La possibilité d'importer des fichiers audio/vidéo directement depuis votre administration wordpress
* La gestion des playlist
* Outil de recherche de vidéo lors de l'écriture d'un article ou d'une page

== Installation ==

Il est nécessaire pour utiliser ce plugin d'avoir un compte VOD sur notre interface d'administration http://statslive.infomaniak.ch/
Pour installer ce plugin, il faut :

1. Envoyer le plugin `vod-infomaniak` dans le dossier `/wp-content/plugins/` de votre blog.
1. Aller activer ce plugin dans le menu plugins de wordpress.
1. Se rendre dans Gestion VOD -> Configuration afin de configurer votre compte avec les identifiants fournis sur l'interface d'administration.

== Frequently Asked Questions ==

= La numérotation du plugin est etrange, ca ne commence pas à la version 1.0 un logiciel? =

Si justement, ce plugin est une préversion encore en cours de developpement. Il passera en version 1.0 dès qu'il sera considéré comme assez stable et abouti.
Il n'y a pas encore de version stable, c'est donc de l'évolution continue et il risque donc de demander assez souvent d'être mis à jour. 

= Il faut que je fournisse mes identifiants personnels au plugin ? =

Pour des raisons de sécurités, il est fortement déconseillé de le faire.
Il est nettement plus prudent dans votre interface d'administration VOD de créer un nouvel utilisateur et de ne lui attribuer que les droits "Gestion API".

= J'ai créé un player, un dossier ou une playlist mais ils n'apparaissent pas encore sur mon blog =

Le plugin est prévu pour se synchroniser régulièrement avec votre compte afin de récupérer les dernières modifications automatiquement.
Il peut cependant arriver que vous n'ayez pas le temps d'attendre que cela ce synchronise automatiquement.
Dans ce cas là, il faut se rendre sur la page Gestion VOD > Configuration et appuyer sur le bouton "Synchronisation rapide".

= J'ai envoyé de nouvelles vidéos mais elles n'apparaissent pas dans la liste des vidéos du blog =

Cela peut provenir d'un problème avec l'adresse de callback. C'est une adresse qu'utilise notre système d'encodage pour prévenir votre blog/site qu'une nouvelle vidéo est disponible.
Cette adresse doit donc être joignable de façon publique. Pour plus d'informations, se reporter à la page Gestion VOD > Configuration

= Je souhaite Géolocaliser une vidéo ou l'interdire à certaines adresses IP =

C'est possible mais pas directement depuis le plugin. Il est pour cela nécessaire de se rendre sur la console d'administration VOD dans "Dossiers -> Diffusion -> Restrictions par zone(s) géographique(s) »

= Je souhaite empêcher d'autres sites d'utiliser mes vidéos sans mon autorisation =

Par défaut les vidéos sont accessibles publiquement et donc utilisable par tout le monde sur n'importe quel site.
Il est toutefois possible de restreindre l'accès aux vidéos d'un dossier seulement à certains utilisateurs.

Pour cela nous fournissons un système de token ou vous êtes seul en mesure de générer une clef unique permettant à un utilisateur de visionner ou non une vidéo.
Ces clefs ne sont valable que pour une adresse IP et une durée limité ce qui empêche le partage de celle-ci.
Cela permet de gérer assez finement l'accès aux vidéos et peut-être très utile dans le cas de pay per view ou d'un site privé.

Cette option est configurable dans la console d'administration dans "Dossiers -> Diffusion -> Restriction par clef unique".
Une fois activé, il faut se rendre sur la page Gestion VOD > Configuration et appuyer sur le bouton "Synchronisation rapide".
Les nouvelles vidéos seront automatiquement ajoutés avec le paramètre "tokenFolder" permettant d'identifier les vidéos nécessitant la génération d'une clef unique.

== Screenshots ==

1. Article créer à l'aide du plugin et dans lequel on peut retrouver une vidéo.
2. Screenshot montrant le menu d'administration permettant de gérer ses vidéos/players/playlist

== Changelog ==

= 0.1.5 =
* Correction concernant l'ecrite d'un post lorsque la vidéo nécéssite un token
* Correction du message indiquant des géolocalisation sur certaines vidéos n'en ayant pas

= 0.1.4 =
* Mise en place d'une premiere version du système de traduction
* Correction dans l'appel d'une fonction de wordpress deprecated

= 0.1.3 =
* Ajout de l'url de l'image
* Option permettant de creer automatiquement un article en draft à partir d'une vidéo
* Amélioration de la page player avec un player de prévisualisation pour pouvoir tester celui selectionné

= 0.1.2 =
* Gestion automatique des token unique sur les vidéos le nécessitant. L'ajout d'un attribut 'tokenfolder' à la balise [vod] permet au plugin d'identifier les vidéos utilisant cette fonctionnalité et ainsi de générer à la volé la clef unique pour chaque visiteur.
* Focus automatique sur les champs des formulaires de recherches

= 0.1.1 =
* Ajout de nouveaux liens sur les vidéos (statistiques, détails d'une vidéo)
* Possibilité de renommer et supprimer une vidéo
* Nouveau système de cryptage du password de l'API

= 0.1 =
* Mise en place de la première version

= Prochainement =
* Gestion des fichiers audio
* Traductions EN/DE
* Une version stable