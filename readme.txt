=== Plugin Name ===
Contributors: vod-infomaniak
Tags: video, manage
Requires at least: 2.8.6
Tested up to: 3.2.1

Insert and Manage Infomaniak VOD's videos in posts, comments and RSS feeds with ease. You need an Infomaniak VOD account to use this plugin.

== Description ==

Attention : Ce plugin est encore en cours de développement, il est fortement déconseillé de l'utiliser dans un environnement de production.

Ce plugin vous permet de gérer facilement les interactions entre votre espace VOD et votre blog.
Il vous permet en toute simplicité de récupérer et de gérer l'ensemble de vos vidéos. 
Il offre également de nombreuses autres options :

* Mise à jour automatique lors de l'ajout de nouvelles vidéos
* Récupération automatique des players existants
* Possibilité d'ajouter des fichiers audio/vidéo directement depuis l'interface d'admin
istration
* Gestion des playlist
* Outil de recherche de vidéo lors de l'écriture d'un article ou d'une page

== Installation ==

Il est nécessaire pour utiliser ce plugin d'avoir un compte VOD sur notre interface d'administration http://statslive.infomaniak.ch/
Pour installer ce plugin, il vous est nécessaire de :

1. Envoyer le plugin `vod-infomaniak` dans le dossier `/wp-content/plugins/`.
1. L'activer dans le menu plugins de wordpress.
1. De se rendre dans Gestion VOD -> Configuration afin de configurer votre compte afin les identifiants fournis sur l'interface d'administration.

== Frequently Asked Questions ==

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

== Changelog ==

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
* Gestion automatiques des répertoires avec Geoip ou token
