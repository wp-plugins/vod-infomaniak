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

Ce plugin offre de nombreuses options supplémentaires, tels que :

* La mise à jour automatique lors de l'ajout de nouvelles vidéos
* La récupération automatique des players existants
* La possibilité d'importer des fichiers audio/vidéo directement depuis votre administration wordpress
* La gestion des playlist
* Outil de recherche de vidéo lors de l'écriture d'un article ou d'une page

Si vous souhaitez obtenir plus d'informations sur notre solution d'hebergement vidéo, veuillez-vous rendre à l'adresse http://streaming.infomaniak.com/stockage-video-en-ligne
 
== Installation ==

Il est nécessaire pour utiliser ce plugin d'avoir un compte VOD sur notre interface d'administration http://statslive.infomaniak.ch/
Pour installer ce plugin, il faut :

1. Envoyer le plugin `vod-infomaniak` dans le dossier `/wp-content/plugins/` de votre blog.
1. Aller activer ce plugin dans le menu plugins de wordpress.
1. Se rendre dans Gestion VOD -> Configuration afin de configurer votre compte avec les identifiants fournis sur l'interface d'administration.

Pour les mises à jours, celles-ci sont automatiques et se font sur le gestionnaire d'extension de wordpress.

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

= Je n'ai pas trouvé de réponse à ma question =

Sur notre site internet, nous possédons une Foire au Question (http://faq.infomaniak.ch/vod) qui répond à un grand nombre de question et propose des guides/tutoriaux vidéos.
Si vous ne trouvez toujours pas de solution à votre question, vous pouvez aussi nous contacter par email.

== Screenshots ==

1. Article créer à l'aide du plugin et dans lequel on peut retrouver une vidéo.
2. Screenshot montrant le menu d'administration permettant de gérer ses vidéos/players/playlist

== Changelog ==

= 0.2.6 (28/09/2011) =
* Suppression d'un warning pouvant apparaitre sur certaines configurations
* Ajout d'accents sur quelques phrases n'etant pas présentes dans les fichiers de traductions
* Remise en forme du formulaire d'ajout d'une vidéo
* Nouveau système d'update automatique des tables mysql
* Ajout de la possibilité d'uploader une vidéo directement lors de l'ecriture d'un article/page via un onglet "Envoi d'une vidéo"
* Modification du loading de certaines fonction par le plugin qui pouvait rentrer en conflit avec certains thèmes.
* Correction d'un probleme sur la récuperation des playlist

= 0.2.4 (14/09/2011) =
* Utilisation du mode de debug de wordpress au lieu d'erreur PHP en cas de problème avec l'API
* Modification du fonctionnement du renommage d'une vidéo

= 0.2.3 (05/09/2011) =
* Ajout d'un try/catch sur la récuperation des importations, cela pouvant provoquer une erreur s'il n'y a aucune importation récente.
* Nouvelle option lors de l'écriture/édition d'un article permettant de choisir parmis les 50 dernieres vidéos.
* Quelques petites optimisations
* Fix d'un bug avec le système de synchro lors d'une première installation et que rien n'a encore été configuré.

= 0.2 (01/09/2011) =
* Modification du nouveau système de synchro pour qu'il se lance plus régulierement contrairement à wp_schedule_event()
* Suppression des notices pouvant être indiqués par apache
* Correction d'un bug d'affichage dans le cas de playlist incomplète
* Nouveau système de synchro automatique des videos plus efficace
* Fix d'un bug durant la synchro forcé des dossiers
* Récuperation et affichage de la durée des playlist
* Correction d'orthographe sur plusieurs phrases
* Snapshot de prévisualisation
* Fix d'un bug d'affichage en 1024
* Correction concernant l'écriture automatique d'un post lorsque la vidéo nécéssite un token
* Correction du message indiquant des géolocalisation sur certaines vidéos n'en ayant pas
* Mise en place d'une première version du système de traduction
* Correction dans l'appel d'une fonction de wordpress deprecated
* Option permettant de créer automatiquement un article en draft à partir d'une vidéo
* Amélioration de la page player avec un player de prévisualisation pour pouvoir tester celui selectionné
* Gestion automatique des token unique sur les vidéos le nécessitant. L'ajout d'un attribut 'tokenfolder' à la balise [vod] permet au plugin d'identifier les vidéos utilisant cette fonctionnalité et ainsi de générer à la volé la clef unique pour chaque visiteur.
* Focus automatique sur les champs des formulaires de recherches
* Ajout de nouveaux liens sur les vidéos (statistiques, détails d'une vidéo)
* Possibilité de renommer et supprimer une vidéo
* Nouveau système de cryptage du password de l'API

= 0.1 (23/08/2011) =
* Lancement du projet et première version publié sur wordpress.org

= Prochainement =
* Traductions EN/DE
* Une version stable
* Gestion des fichiers audio