<?php
/*
Plugin Name: VOD Infomaniak
Plugin URI: http://infomaniak.ch
Description: Insert and Manage Infomaniak VOD's videos in posts, comments and RSS feeds with ease and full customization. You need an Infomaniak VOD account to use this plugin.
Author: Destrem Kevin
Version: 0.1
Author URI: http://infomaniak.ch
*/

if (isset($oVod)) return false;
require_once(dirname(__FILE__) . '/vod.class.php');
$oVod = new EasyVod();
