=== Plugin Name ===
Contributors: vod-infomaniak
Tags: video, manage
Requires at least: 2.8.6
Tested up to: 3.2.1

Insert and Manage Infomaniak VOD's videos in posts, comments and RSS feeds with ease and full customization.
You need an Infomaniak VOD account to use this plugin.

== Description ==

Attention : Ce plugin est encore en cours de développement, il est fortement déconseillé de l'utiliser dans un environnement de production.

Ce plugin vous permet de gérer facilement les interactions entre votre espace VOD et votre blog.
Il vous permet en toute simplicité de récupérer et de gérer l'ensemble de vos vidéos. 
Il offre également de nombreuses autres options :

* Mise à jour automatique lors de l'ajout de nouvelles vidéos
* Récupération automatique des players existants
* Possibilité d'uploader les fichiers depuis l'interface d'admin
* Gestion des playlist
* Outil de recherche de vidéo lors de l'écriture d'un article ou d'une page 

== Installation ==

Il est nécéssaire pour utiliser ce plugin d'avoir un compte VOD sur notre interface d'administration http://statslive.infomaniak.ch/
Pour installer ce plugin, il vous est nécéssaire de :

1. Envoyer le plugin `vod-infomaniak` dans le dossier `/wp-content/plugins/`.
1. L'activer dans le menu plugins de wordpress.
1. De se rendre dans Gestion VOD -> Configuration afin de configurer votre compte afin les identifiants fournis sur l'interface d'administration.

== Frequently Asked Questions ==

= Il faut que je fournisse mes identifiants personnels au plugin ? =

Pour des raisons de sécurités, il est fortement déconseillé de le faire.
Il est nettement plus prudent dans votre interface d'administration VOD de créer un nouvel utilisateur et de ne lui attribuer que les droits "Gestion API".

= J'ai créé un player, un dossier ou une playlist mais il n'apparait pas encore sur mon blog =

Le plugin est prévu pour se synchroniser régulièrement avec votre compte afin de récupérer les dernières modifications automatiquement.
Il peut cependant arriver que vous n'ayez pas le temps d'attendre que cela ce synchronise automatiquement.
Dans ce cas là, il faut se rendre sur la page Gestion VOD > Configuration et appuyer sur le bouton "Synchronisation rapide".

= J'ai envoyé de nouvelles vidéos mais elles n'apparaissent pas dans la liste des vidéos du blog =

Cela peut provenir d'un problème avec l'adresse de callback. C'est une adresse qu'utilise notre système d'encodage pour prévenir votre blog/site qu'une nouvelle vidéo est disponible.
Cette adresse doit donc être joignable de façon publique. Pour plus d'informations, se reporter à la page Gestion VOD > Configuration

== Changelog ==

= 0.1 =
* First release

= Prochainement =

* Gestion des fichiers audios
* Traductions EN/DE
* Gestion automatiques des repertoires avec Geoip ou token