<?php
/**
 * Classe generale regroupant les differentes fonctions du plugin wordpress.
 * En cas de problemes ou de questions, veuillez contacter streaming@infomaniak.ch
 *
 * @author Destrem Kevin
 * @link http://statslive.infomaniak.ch/vod/api/
 * @version 1.0
 * @copyright infomaniak.ch
 *
 */

class EasyVod
{
	var $local_version;
	var $plugin_url;
	var $options;
	var $key;
	var $db;

	function EasyVod() {
		$this->__construct();
	}
	
	function __construct() {
		$this->local_version = '0.1';
		$this->key = 'vod_infomaniak';
		$this->gds = '#vod.$-';
		$this->options=$this->get_options();
		$this->add_filters_and_hooks();
		$this->db = new EasyVod_db();
		$this->auto_sync = true;
		$this->auto_sync_delay = 6*3600;
	}

	function add_filters_and_hooks() {
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_style( 'vvq-jquery-ui', plugins_url('/vod/css/jquery-ui.css'), array(), $this->version, 'screen' );
		
		add_action( 'admin_footer', array(&$this, 'buildForm') );

		if ($this->options['posts'] == 'on')  
		{
			add_filter('the_content', array(&$this, 'check'), 100);
			add_filter('the_excerpt', array(&$this, 'check'), 100);
		}
		
		if ($this->options['comments'] == 'on') 
		{
			add_filter('comment_text', array(&$this, 'check'), 100);
		}
		
		add_action('admin_menu', array($this, 'add_menu_items'));
		register_activation_hook(__FILE__, array(&$this, 'install_db') );

		add_filter('mce_external_plugins', array(&$this, 'mce_register') );
		add_filter('mce_buttons', array(&$this, 'mce_add_button'), 0);
		
		// Add Admin Page
		add_action( 'wp_ajax_importvod', array(&$this, 'printLastImport') );
		add_action( 'wp_ajax_vodsearchvideo', array(&$this, 'searchVideo') );
		add_action( 'wp_ajax_vodsearchplaylist', array(&$this, 'searchPlaylist') );

		add_filter('query_vars', 'vod_query_vars');
		add_action('template_redirect', 'vod_template_redirect');

		//Register
		wp_register_style('ui-tabs', plugins_url('/vod/css/jquery.ui.tabs.css'));

		//On load Css et Js
		wp_enqueue_script("jquery-ui-tabs");
		wp_enqueue_script( 'suggest' );	
		wp_enqueue_style('ui-tabs');
	}
	
	function install_db() {
		$this->db->install_db();
		if( $this->auto_sync ){
			$this->checkAutoUpdate();
		}
	}

	function searchPlaylist() {
		$aResult = $this->db->search_playlist($_REQUEST['q'], 12);
		foreach( $aResult as $oPlaylist ){
			echo "<span style='display:none'>".$oPlaylist->iPlaylistCode.";;;</span><span>".$oPlaylist->sPlaylistName."</span>\n";
		}
		die();
	}

	function searchVideo() {
		$aResult = $this->db->search_videos($_REQUEST['q'], 12);
		foreach( $aResult as $oVideo ){
			$str = "";
			$duration = intval($oVideo->iDuration/100);
			$hour = intval($duration/3600);
			$min = intval($duration/60)%60;
			$sec = intval($duration)%60;
			
			$str .= $hour>0 ? $hour."h. " : '';
			$str .= $min>0 ? $min."m. " : '';
			$str .= $sec>0 ? $sec."s." : '';
			
			echo "<span style='display:none'>".$oVideo->sPath.$oVideo->sServerCode.".".strtolower($oVideo->sExtension).";;;</span><span>".ucfirst($oVideo->sName)." ";
			echo "( Ajout: ".date("j F Y ", strtotime($oVideo->dUpload)).", Durée: $str )</span>\n";
		}
		die();
	}

	function add_menu_items() {
		if (function_exists('add_menu_page')) {
			add_menu_page('Gestion VOD', 'Gestion VOD', 8, __FILE__, array(&$this,'vod_management_menu'));
		}

		if (function_exists('add_submenu_page')) {
			add_submenu_page(__FILE__,'Gestionnaire', 'Gestionnaire', 8, __FILE__, array(&$this,'vod_management_menu'));
			add_submenu_page(__FILE__,'Importer une video', 'Importer une video', 8, 'import', array(&$this,'vod_upload_menu'));
			add_submenu_page(__FILE__,'Implementation', 'Implementation', 8, 'implementation', array(&$this,'vod_implementation_menu'));
			add_submenu_page(__FILE__,'Playlist', 'Playlist', 8, 'Playlist', array(&$this,'vod_playlist_menu'));
			add_submenu_page(__FILE__,'Configuration', 'Configuration', 8, 'configuration', array(&$this,'vod_admin_menu'));
		}		
	}

	function check($the_content, $side = 0) {
		$tag=$this->options['tag'];
		if ($tag!='' && strpos($the_content, "[".$tag) !== false ) {
			preg_match_all("/\[$tag([^`]*?)\]([^`]*?)\[\/$tag\]/", $the_content, $matches, PREG_SET_ORDER);
			foreach ($matches as $match) {
				$the_content = preg_replace("/\[$tag([^`]*?)\]([^`]*?)\[\/$tag\]/", $this->tag($match[2], $match[1], '', '', $side), $the_content, 1);
			}
		}
		return $the_content;
	}
	
	function tag($file, $params, $high = 'v', $time = '', $side = 0) {	
		//Recuperation des parametres optionnels des tags
		$aTagParam = array();
		if ( !empty( $params ) ) {
					
			$params = strtolower(str_replace(array("'",'"'), "", $params));
			$aList = split(" ", $params);
			foreach( $aList as $param) {
				if( strpos($param, "=") !== false  ) {
					$aCut = split("=", $param);
					if( in_array($aCut[0] ,array("width", "height", "autoplay", "loop", "player", "videoimage") )){
						$aTagParam[ $aCut[0] ] = $aCut[1];
					}
				}
			}
		}
		//Recuperation des differents parametres
		$iVod		= $this->options['vod_api_icodeservice'];
		$sUrl		= "http://vod.infomaniak.com/iframe.php";
		$sAccountBase	= $this->options['vod_api_id'];
				
		$videoimage	= empty( $aTagParam['videoimage'] ) ? 1 : intval($aTagParam['videoimage']);
		$player		= empty( $aTagParam['player'] ) ? $this->options['player'] : intval($aTagParam['player']);
		$autoplay	= empty( $aTagParam['autoplay'] ) ? $this->options['autoplay'] : intval($aTagParam['autoplay']);
		$loop		= empty( $aTagParam['loop'] ) ? $this->options['loop'] : intval($aTagParam['loop']);
		$width		= empty( $aTagParam['width'] ) ? $this->options['width'] : intval($aTagParam['width']);
		$height		= empty( $aTagParam['height'] ) ? $this->options['height'] : intval($aTagParam['height']);

		if ( is_numeric( $file ) ) {
			$video_url = $sUrl."?url=&playlist=".$file;
		} else {
			//Build de l'url finale
			if ( strpos($file, "http://") === false ) {
				$sFile = $sAccountBase."/".$file;
			} else {
				$sFile = $file;
			}
			$video_url = $sUrl."?url=".$sFile;
			if( $videoimage ) $video_url .= "&preloadImage=".str_replace(array(".flv",".mp4"), ".jpg", $sFile);
		}	
		if( !empty($player) ){
			$video_url .= "&player=$player";
		}else{
			$video_url .= "&player=576";
		}
		if( $iVod ) $video_url .= "&vod=$iVod";
		$video_url .= "&autostart=$autoplay";
		$video_url .= "&loop=$loop";

		//Build de la balise
		$html_tag = '<span class="youtube">
		<iframe title="Vod Player" class="vod-player" width="'.$width.'" height="'.$height.'" src="'.$video_url.'" frameborder="0"></iframe>
		</span>';

		return $html_tag;
	}

	function get_options() {
		$options = array(
			'posts'		=> 'on',
			'comments'	=> 'off',
			'img'		=> 'off',
			'width'		=> 425,
			'height'	=> 344,
			'template'	=> '{video}',
			'loop'		=> 0,
			'autoplay'	=> 0,
			'privacy'	=> 0,
			'wtext'		=> '',
			'wtitle'	=> '',
			'tag'		=> 'vod',
			'iframe'	=> 'on',
			'vod_api_connected' => 'off'
		);
              
		$saved = get_option($this->key);
              
		if (!empty($saved)) {
			foreach ($saved as $key => $option){
				$options[$key] = $option;
			}
		}
              
		if ($saved != $options){
			update_option($this->key, $options);
		}
              
		return $options;
	}

	function mce_add_button($buttons) {
		array_push($buttons, "vodplugin");
		return $buttons;
	}
	 
	function mce_register($plugin_array) {
		$plugin_array["vodplugin"] = plugins_url('/vod/js/editor_plugin.js');
		return $plugin_array;
	}

	function buildForm() {
		if ( !empty($this->options['vod_api_connected']) && $this->options['vod_api_connected'] == 'on' ) {
			require_once("vod.template.php");
			$aPlayers = $this->db->get_players();
			EasyVod_Display::buildForm( $this->options, $aPlayers );
		}
	}

	// This function always return FALSE (who woulda guessed?)
	function ReturnFalse() { return FALSE; }

	function checkAutoUpdate(){
		$gmtime = time() - (int)substr(date('O'),0,3)*60*60;
		if ( $this->options['vod_api_lastUpdate'] < $gmtime - $this->auto_sync_delay ) {			
			$oApi = $this->getAPI();
			
			//Update des players
			if ( $oApi->playerModifiedSince( $this->options['vod_api_lastUpdate'] ) ) {
				$this->db->clean_players();				
				$aListPlayer = $oApi->getPlayers();
				foreach( $aListPlayer as $oPlayer ){
					if( empty( $this->options['player'] ) ) {
						$this->options['player'] = $oPlayer['iPlayerCode'];
					}
					$this->db->insert_player( $oPlayer['iPlayerCode'], $oPlayer['sName'], $oPlayer['iWidth'], $oPlayer['iHeight'], $oPlayer['bAutoStart'], $oPlayer['bLoop'], $oPlayer['dEdit'], $oPlayer['bSwitchQuality'] );
				}
			}

			//Update des folders
			if ( $oApi->folderModifiedSince( $this->options['vod_api_lastUpdate'] ) ) {
				$this->db->clean_folders();				
				$aListFolder = $oApi->getFolders();
				foreach( $aListFolder as $oFolder ){
					$this->db->insert_folder( $oFolder['iFolderCode'], $oFolder['sFolderPath'], $oFolder['sFolderName'] );
				}
			}

			//Update des playlist
			if ( $oApi->playlistModifiedSince( $this->options['vod_api_lastUpdate'] ) ) {
				$this->db->clean_playlists();				
				$aListPlaylist = $oApi->getPlaylists();
				foreach( $aListPlaylist as $oPlaylist ){
					$this->db->insert_playlist( $oPlaylist['iPlaylistCode'], $oPlaylist['sPlaylistName'], $oPlaylist['sPlaylistDescription'], $oPlaylist['iTotal'], $oPlaylist['sMode'], $oPlaylist['dCreated'] );
				}
			}

			$this->options['vod_api_lastUpdate'] = $gmtime;
			update_option($this->key, $this->options);
		}
	}

	function vod_admin_menu(){
		$site_url = get_option("siteurl");

		if (isset($_POST['submitted'])) {
			$bResult = false;
			if ( empty( $this->options['vod_api_callbackKey']) ) {
				$this->options['vod_api_callbackKey'] = sha1( time() * rand() );
			}
			if ( empty( $this->options['vod_api_c']) ) {
				$this->options['vod_api_c'] = md5( time() * rand() );
			}

			$this->options['vod_api_login'] = stripslashes(htmlspecialchars( $_POST['vod_api_login'] ));
			if ( isset($_POST['vod_api_password']) && $_POST['vod_api_password'] != "XXXXXX" ) {
				$this->options['vod_api_password'] = $this->encrypt( stripslashes(htmlspecialchars( $_POST['vod_api_password'] )), $this->gds.$this->options['vod_api_c']);
			}
			$this->options['vod_api_id'] = 	stripslashes(htmlspecialchars( $_POST['vod_api_id'] ));
			$this->options['vod_api_connected'] = 'off';
			
			try {
				$oApi = $this->getAPI();
				$bResult = $oApi->ping();
				if( $bResult ){
					$this->options['vod_api_connected'] = 'on';
					$this->options['vod_api_icodeservice'] = $oApi->getServiceItemID();
					$this->options['vod_api_group'] = $oApi->getGroupID();
					$this->options['vod_api_lastUpdate'] = 0;
					
					//Verification DB et synchro
					$this->install_db(); 
					if ( empty($this->options['vod_api_valid_callback']) || $this->options['vod_api_valid_callback'] == 'off' ) {
						$sUrl = $oApi->getCallback();
						if ( empty( $sUrl ) || strpos( $sUrl, $site_url )!==false ) {
							$site_url = str_replace("http://","", $site_url);
							$oApi->setCallback( $site_url."/?vod_page=callback&key=".$this->options['vod_api_callbackKey'] );
							$this->options['vod_api_valid_callback'] == 'on';
						} else {
							$this->options['vod_api_valid_callback'] == 'off';
						}
					}
					if( $this->db->count_video() == 0 ){
						$oApi = $this->getAPI();
						
						//Update des videos
						$iNumberVideoApi = 200;
						$this->db->clean_videos();
						$iVideo = $oApi->countVideo();
						$iPageTotal = floor( ($iVideo-1) / $iNumberVideoApi );
						for( $iPage=0; $iPage <= $iPageTotal; $iPage++ ) {
							$aVideos = $oApi->getLastVideo($iNumberVideoApi, $iPage*$iNumberVideoApi);
							foreach( $aVideos as $oVideo ) {
								$this->db->insert_video( $oVideo['iFileCode'], $oVideo['iFolder'], $oVideo['sFileName'], $oVideo['sFileServerCode'], $oVideo['aEncodes'][0]['sPath'], $oVideo['aEncodes'][0]['eConteneur'], $oVideo['fFileDuration'], $oVideo['dFileUpload'] );
							}
						}
					}
				}
			} catch (Exception $oException) {
				echo "<h4 style='color: red;'>Erreur : Impossible de se connecter</h4>";
			}
			update_option($this->key, $this->options);
		}
		if (isset($_POST['updateSynchro']) && $_POST['updateSynchro'] == 1 ) {
			$oApi = $this->getAPI();
			
			//Update des players
			$this->db->clean_players();				
			$aListPlayer = $oApi->getPlayers();
			foreach( $aListPlayer as $oPlayer ){
				$this->db->insert_player( $oPlayer['iPlayerCode'], $oPlayer['sName'], $oPlayer['iWidth'], $oPlayer['iHeight'], $oPlayer['bAutoStart'], $oPlayer['bLoop'], $oPlayer['dEdit'], $oPlayer['bSwitchQuality'] );
			}

			//Update des folders
			$this->db->clean_folders();				
			$aListFolder = $oApi->getFolders();
			foreach( $aListFolder as $oFolder ){
				$this->db->insert_folder( $oFolder['iFolderCode'], $oFolder['sFolderPath'], $oFolder['sFolderName'] );
			}

			//Update des playlist
			$this->db->clean_playlists();				
			$aListPlaylist = $oApi->getPlaylists();
			foreach( $aListPlaylist as $oPlaylist ){
				$this->db->insert_playlist( $oPlaylist['iPlaylistCode'], $oPlaylist['sPlaylistName'], $oPlaylist['sPlaylistDescription'], $oPlaylist['iTotal'], $oPlaylist['sMode'], $oPlaylist['dCreated'] );
			}

			$gmtime = time() - (int)substr(date('O'),0,3)*60*60;
			$this->options['vod_api_lastUpdate'] = $gmtime;
			update_option($this->key, $this->options);
		}
		if (isset($_POST['updateSynchroVideo']) && $_POST['updateSynchroVideo'] == 1 ) {
			$oApi = $this->getAPI();
						
			//Update des videos
			$iNumberVideoApi = 200;
			$this->db->clean_videos();
			$iVideo = $oApi->countVideo();
			$iPageTotal = floor( ($iVideo-1) / $iNumberVideoApi );
			for( $iPage=0; $iPage <= $iPageTotal; $iPage++ ) {
				$aVideos = $oApi->getLastVideo($iNumberVideoApi, $iPage*$iNumberVideoApi);
				foreach( $aVideos as $oVideo ) {
					$this->db->insert_video( $oVideo['iFileCode'], $oVideo['iFolder'], $oVideo['sFileName'], $oVideo['sFileServerCode'], $oVideo['aEncodes'][0]['sPath'], $oVideo['aEncodes'][0]['eConteneur'], $oVideo['fFileDuration'], $oVideo['dFileUpload'] );
				}
			}
		}

		if ( $this->options['vod_api_connected'] == "on" ) {
			$this->options['vod_count_player'] = $this->db->count_player();
			$this->options['vod_count_folder'] = $this->db->count_folder();
			$this->options['vod_count_video'] = $this->db->count_video();
			$this->options['vod_count_playlist'] = $this->db->count_playlists();
		}

		$actionurl   = $_SERVER['REQUEST_URI'];
		require_once("vod.template.php");
		EasyVod_Display::adminMenu( $actionurl, $this->options, $site_url);
	}

	function vod_management_menu() {
		if ( empty($this->options['vod_api_connected']) || $this->options['vod_api_connected'] == 'off' ) {
			echo "<h2>Problème de configuration</h2><p>Veuillez-vous rendre dans Gestion VOD -> Configuration afin de configurer votre compte.</p>";
		} else {
			$iPage = !empty($_REQUEST['p']) ? intval( $_REQUEST['p'] ) : 1;
			$iLimit = 20;
			$iVideoTotal = $this->db->count_video();
			$aVideos = $this->db->get_videos_byPage($iPage-1, $iLimit);
			require_once("vod.template.php");
			$sPagination = EasyVod_Display::buildPagination( $iPage, $iLimit, $iVideoTotal );
			EasyVod_Display::managementMenu( $sPagination, $this->options, $aVideos );
		}
	}

	function vod_upload_menu(){
		if ( empty($this->options['vod_api_connected']) || $this->options['vod_api_connected'] == 'off' ) {
			echo "<h2>Problème de configuration</h2><p>Veuillez-vous rendre dans Gestion VOD -> Configuration afin de configurer votre compte.</p>";
		} else {
			require_once("vod.template.php");
			if ( $_REQUEST['sAction'] == "popupUpload" && !empty($_REQUEST['iFolderCode']) ) {
				//Affichage du popup d'upload				
				$oFolder = $this->db->getFolder( $_REQUEST['iFolderCode'] );
				if( empty($oFolder) || empty( $oFolder->sName ) ){
					die("Il n'est pas possible d'uploader dans ce dossier.");
				}
				$oApi = $this->getAPI();
				$sToken = $oApi->initUpload( $oFolder->sPath );
				EasyVod_Display::uploadPopup( $sToken, $oFolder );
			} else if( $_REQUEST['sAction'] == "popupImport" && !empty($_REQUEST['iFolderCode']) ) {
				//Affichage du popup d'import
				$bResult = false;				
				$oFolder = $this->db->getFolder( $_REQUEST['iFolderCode'] );
				if( empty($oFolder) || empty( $oFolder->sName ) ){
					die("Il n'est pas possible d'uploader dans ce dossier.");
				}
				if( $_REQUEST['submit'] == 1 ){
					$oApi = $this->getAPI();
					$aOptions = array();
					if ( !empty($_REQUEST['sLogin']) && !empty($_REQUEST['sPassword']) ) {
						$aOption['login'] = $_REQUEST['sLogin'];
						$aOption['password'] = $_REQUEST['sPassword'];
					}
					$sUrl = $_REQUEST['sProtocole']."://".$_REQUEST['sUrl'];
					$bResult = $oApi->importFromUrl( $oFolder->sPath, $sUrl , $aOption);
				}
				$actionurl = $_SERVER['REQUEST_URI'];
				EasyVod_Display::ImportPopup( $actionurl, $oFolder, $bResult);
			} else {
				//Affichage de la page principal
				$aFolders = $this->db->get_folders();
				
				$actionurl = $_SERVER['REQUEST_URI'];
				EasyVod_Display::uploadMenu( $actionurl, $this->options, $aFolders, $this->getLastImport() );
			}
		}
	}

	function printLastImport() {
		echo $this->getLastImport();
		die();
	}

	function getLastImport() {
		require_once("vod.template.php");
		$aLastImport = get_transient( 'vod_last_import' );
		if ( false == $aLastImport ) {
			$oApi = $this->getAPI();
			$aLastImport = $oApi->getLastImportation();
			set_transient( 'vod_last_import', $aLastImport, 15 );
		}
		return EasyVod_Display::tabLastUpload( $aLastImport );
	}

	function vod_playlist_menu(){
		if ( empty($this->options['vod_api_connected']) || $this->options['vod_api_connected'] == 'off' ) {
			echo "<h2>Problème de configuration</h2><p>Veuillez-vous rendre dans Gestion VOD -> Configuration afin de configurer votre compte.</p>";
		} else {
			require_once("vod.template.php");
			$aPlaylist = $this->db->get_playlists();
			EasyVod_Display::playlistMenu( $actionurl, $this->options, $aPlaylist );
		}
	}

	function vod_implementation_menu(){
		if ( empty($this->options['vod_api_connected']) || $this->options['vod_api_connected'] == 'off' ) {
			echo "<h2>Problème de configuration</h2><p>Veuillez-vous rendre dans Gestion VOD -> Configuration afin de configurer votre compte.</p>";
		} else {
			require_once("vod.template.php");
			if (isset($_POST['submitted'])) {
				$oPlayer = $this->db->get_player( intval($_REQUEST['selectPlayer']) );
				if ( !empty($oPlayer) ) {
					$this->options['player'] = $oPlayer->iPlayer;
					$this->options['width'] = $oPlayer->iWidth;
					$this->options['height'] = $oPlayer->iHeight;
					$this->options['autoplay'] = $oPlayer->bAutoPlay;
					$this->options['loop'] = $oPlayer->bLoop;
					update_option($this->key, $this->options);
				}
			}
			$aPlayers = $this->db->get_players();
			EasyVod_Display::implementationMenu( $actionurl, $this->options, $aPlayers );
		}
	}

	function getAPI() {		
		require_once('vod.api.php');
		$sPassword = $this->decrypt($this->options['vod_api_password'], $this->gds.$this->options['vod_api_c']);		
		return new vod_api($this->options['vod_api_login'], $sPassword, $this->options['vod_api_id']);
	}

	function encrypt($str, $key) {
		$block = mcrypt_get_block_size('des', 'ecb');
		$pad = $block - (strlen($str) % $block);
		$str .= str_repeat(chr($pad), $pad);
		return base64_encode(mcrypt_encrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB));
	}

	function decrypt($str, $key) {  
		$str = base64_decode( $str );
		$str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
		$block = mcrypt_get_block_size('des', 'ecb');
		$pad = ord($str[($len = strlen($str)) - 1]);
		return substr($str, 0, strlen($str) - $pad);
	}

}

class EasyVod_db
{
	var $db_table_player;
	var $db_table_folder;
	var $db_table_video;
	var $db_table_playlist;

	function __construct() {
		global $wpdb;
		$this->db_table_player = $wpdb->prefix . "vod_player";
		$this->db_table_folder = $wpdb->prefix . "vod_folder";
		$this->db_table_video = $wpdb->prefix . "vod_video";
		$this->db_table_playlist = $wpdb->prefix . "vod_playlist";
	}

	function install_db() {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		$sql_player = "CREATE TABLE ".$this->db_table_player." (
		 `iPlayer` INT UNSIGNED NOT NULL ,
		 `sName` VARCHAR( 255 ) NOT NULL ,
		 `iWidth` INT UNSIGNED NOT NULL ,
		 `iHeight` INT UNSIGNED NOT NULL ,
		 `bAutoPlay` TINYINT UNSIGNED NOT NULL ,
		 `bLoop` TINYINT UNSIGNED NOT NULL,
		 `bSwitchQuality` TINYINT UNSIGNED NOT NULL,
		 `dEdit` DATETIME NOT NULL
		) CHARACTER SET utf8;";		
		dbDelta($sql_player);

		$sql_folder = "CREATE TABLE ".$this->db_table_folder." (
		 `iFolder` INT UNSIGNED NOT NULL ,
		 `sPath` VARCHAR( 255 ) NOT NULL ,
		 `sName` VARCHAR( 255 ) NOT NULL 
		) CHARACTER SET utf8;";
		dbDelta($sql_folder);

		$sql_video = "CREATE TABLE ".$this->db_table_video." (
		 `iVideo` INT UNSIGNED NOT NULL ,
		 `iFolder` INT UNSIGNED NOT NULL ,
		 `sName` VARCHAR( 255 ) NOT NULL ,
		 `sPath` VARCHAR( 255 ) NOT NULL,
		 `sServerCode` VARCHAR( 255 ) NOT NULL,
		 `sExtension` VARCHAR( 4 ) NOT NULL,
		 `iDuration` INT UNSIGNED NOT NULL,
		 `dUpload` DATETIME NOT NULL 
		) CHARACTER SET utf8;";
		dbDelta($sql_video);

		$sql_playlist = "CREATE TABLE ".$this->db_table_playlist." (
		 `iPlaylistCode` INT UNSIGNED NOT NULL ,
		 `sPlaylistName` VARCHAR( 255 ) NOT NULL ,
		 `sPlaylistDescription` VARCHAR( 255 ) NOT NULL ,
		 `iTotal`INT UNSIGNED NOT NULL,
		 `sMode` VARCHAR( 255 ) NOT NULL,
		 `dCreated` DATETIME NOT NULL 
		) CHARACTER SET utf8;";
		dbDelta($sql_playlist);
	}

	/*
	* Gestion des players
	*/
	function get_players() {
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM ".$this->db_table_player);
	}

	function get_player( $iPlayer ) {
		global $wpdb;
		return $wpdb->get_row("SELECT * FROM ".$this->db_table_player." WHERE iPlayer=".intval($iPlayer)." LIMIT 1");
	}

	function clean_players() {
		global $wpdb;
		return $wpdb->query("DELETE FROM ".$this->db_table_player);
	}

	function insert_player( $iPlayer, $sName, $iWidth, $iHeight, $bStart, $bLoop, $dEdit, $bSwitchQuality ) {
		global $wpdb;
		$wpdb->insert( $this->db_table_player, array( 'iPlayer' => $iPlayer, 'sName' => $sName, 'iWidth' => $iWidth, 'iHeight' => $iHeight, 'bAutoPlay' => $bStart, 'bLoop' => $bLoop, 'dEdit' => $dEdit, 'bSwitchQuality' => $bSwitchQuality ) );
	}
	
	function count_player() {
		global $wpdb;
		return $wpdb->get_var("SELECT COUNT(*) FROM ".$this->db_table_player);
	}

	/*
	* Gestion des playlist
	*/
	function search_playlist( $sTerm, $iLimit=6) {
		global $wpdb;
		$sql = $wpdb->prepare("SELECT * FROM ".$this->db_table_playlist." WHERE sPlaylistName LIKE %s OR sPlaylistDescription LIKE %s ORDER BY dCreated DESC LIMIT ".intval($iLimit), "%".$sTerm."%", "%".$sTerm."%");
		return $wpdb->get_results($sql);
	}

	function get_playlists() {
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM ".$this->db_table_playlist);
	}

	function clean_playlists() {
		global $wpdb;
		return $wpdb->query("DELETE FROM ".$this->db_table_playlist);
	}

	function insert_playlist( $iPlaylistCode, $sPlaylistName, $sPlaylistDescription, $iTotal, $sMode, $dCreated ) {
		global $wpdb;
		$wpdb->insert( $this->db_table_playlist, array( 'iPlaylistCode' => $iPlaylistCode, 'sPlaylistName' => $sPlaylistName, 'sPlaylistDescription' => $sPlaylistDescription, 'iTotal' => $iTotal, 'sMode' => $sMode, 'dCreated' => $dCreated ) );
	}

	function count_playlists() {
		global $wpdb;
		return $wpdb->get_var("SELECT COUNT(*) FROM ".$this->db_table_playlist);
	}

	/*
	* Gestion des dossiers
	*/
	function getFolder( $iFolder ) {
		global $wpdb;
		return $wpdb->get_row("SELECT * FROM ".$this->db_table_folder." WHERE iFolder=".intval($iFolder)." LIMIT 1");
	}

	function get_folders() {
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM ".$this->db_table_folder." ORDER BY `sPath` ASC");
	}

	function clean_folders() {
		global $wpdb;
		return $wpdb->query("DELETE FROM ".$this->db_table_folder);
	}

	function insert_folder( $iFolder, $sPath, $sName ) {
		global $wpdb;
		$wpdb->insert( $this->db_table_folder, array( 'iFolder' => $iFolder, 'sPath' => $sPath, 'sName' => $sName ) );
	}

	function count_folder() {
		global $wpdb;
		return $wpdb->get_var("SELECT COUNT(*) FROM ".$this->db_table_folder);
	}

	/*
	* Gestion des videos
	*/
	function search_videos( $sTerm, $iLimit=6) {
		global $wpdb;
		$sql = $wpdb->prepare("SELECT * FROM ".$this->db_table_video." WHERE sName LIKE %s OR sServerCode LIKE %s ORDER BY dUpload DESC LIMIT ".intval($iLimit), "%".$sTerm."%", "%".$sTerm."%");
		return $wpdb->get_results($sql);
	}

	function get_videos_byPage( $iPage, $iLimit ) {
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM ".$this->db_table_video." ORDER BY `dUpload` DESC LIMIT ".intval($iPage*$iLimit).", ".intval($iLimit));
	}

	function get_videos_byCodes( $sServerCode, $iFolderCode ) {
		global $wpdb;
		$sql = $wpdb->prepare("SELECT * FROM ".$this->db_table_video." WHERE sServerCode=%s AND iFolder=%d", $sServerCode, $iFolderCode);
		return $wpdb->get_results($sql);
	}

	function get_videos() {
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM ".$this->db_table_video." ORDER BY `dUpload` DESC");
	}

	function clean_videos() {
		global $wpdb;
		return $wpdb->query("DELETE FROM ".$this->db_table_video);
	}

	function insert_video( $iVideo, $iFolder, $sName, $sServerCode, $sPath, $sExtension, $iDuration, $dUpload ) {
		global $wpdb;
		$wpdb->insert( $this->db_table_video, array( 'iVideo' => $iVideo, 'iFolder' => $iFolder, 'sName' => $sName, 'sServerCode' => $sServerCode, 'sPath' => $sPath, 'sExtension' => $sExtension, 'iDuration' => $iDuration, 'dUpload' => $dUpload) );
	}

	function count_video() {
		global $wpdb;
		return $wpdb->get_var("SELECT COUNT(*) FROM ".$this->db_table_video);
	}

	function delete_video( $iVideo = -1) {
		global $wpdb;
		return $wpdb->query("DELETE FROM ".$this->db_table_video." WHERE iVideo = ".intval($iVideo)." LIMIT 1");
	}
	
}

function vod_query_vars($qvars) {
    $qvars[] = 'vod_page';
    return $qvars;
}

function vod_template_redirect() {
    global $wp_query;
    $vod_page = $wp_query->query_vars['vod_page'];
    if ($vod_page == 'callback') {
        include(ABSPATH.'wp-content/plugins/vod/vod_callback.php');
        exit;
    }
}
?>
