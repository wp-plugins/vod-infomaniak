<?php
/*
Plugin Name: VOD Infomaniak
Plugin URI: http://infomaniak.ch
Description: Easily embed and manage videos from Infomaniak VOD in your posts, comments and RSS feeds. You need an Infomaniak VOD account to use this plugin.
Author: Destrem Kevin
Version: 0.1.3
Author URI: http://infomaniak.ch
*/

if (isset($oVod)) return false;
require_once(dirname(__FILE__) . '/vod.class.php');
$oVod = new EasyVod();
