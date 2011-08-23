<?php
/**
 * Classe d'affichage regroupant les differents templates html/js
 * En cas de problemes ou de questions, veuillez contacter streaming@infomaniak.ch
 *
 * @author Destrem Kevin
 * @link http://statslive.infomaniak.ch/vod/api/
 * @version 1.0
 * @copyright infomaniak.ch
 *
 */

class EasyVod_Display
{

	// Output the <div> used to display the dialog box
	static function buildForm( $options, $aPlayers ) { 
	?>
	<div class="hidden">
		<div id="dialog-vod-form">
			<div id="dialog-tabs" class="ui-tabs">
				<ul class="ui-tabs-nav">
					<li><a href="#dialog-tab1">Avec l'url</a></li>
					<li><a href="#dialog-tab2">Outil de recherche</a></li>
				</ul>
				<div id="dialog-tab1" class="ui-tabs-panel">
					<div style="padding-left: 20px; padding-bottom: 10px;">Veuillez saisir l'URL d'une vidéo</div>
					<div style="padding-left: 20px;">
						<strong>Exemple :</strong>
						<ul id="dialog-exemple">
							<li>Url complete : <code>http://vod.infomaniak.com/redirect/infomaniak_vod1/folder-234/mp4-148/video.mp4</code>
							<li>Url partiel : <code>folder-234/mp4-148/video.mp4</code></li>
							<li>Identifiant de playlist : <code>25</code></li>
						</ul>
					</div>
					<p style="text-align:center"><input type="text" id="dialog-url-input"/></p>
				</div>
				<div id="dialog-tab2" class="ui-tabs-panel">
					<input type="hidden" id="url_ajax_search_video" value="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php?action=vodsearchvideo"/>
					<input type="hidden" id="url_ajax_search_playlist" value="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php?action=vodsearchplaylist"/>
					<div style="padding-left: 30px;">
						<label>Recherche d'une :</label><br/>
						<input type="radio" name="searchtype" id="video" value="video" checked="checked" onclick="checkSearchType();"> <label for="video">Video</label>
						<input type="radio" name="searchtype" id="playlist" value="playlist" onclick="checkSearchType();"> <label for="playlist">Playlist</label>
						<br/>
					</div>
					<p style="text-align:center">
						<input id="dialog-search-input-video" class="dialog-search-input"/>
						<input id="dialog-search-input-playlist" class="dialog-search-input"/>
					</p>
				</div>
			</div>
			<div id="dialog-config">
				<div id="dialog-slide-header" class="ui-dialog-titlebar" onclick="Vod_dialogToggleSlider();">Options d'intégration</div>
				<div id="dialog-slide" style="display:none">
					<p class="dialog-form-line">
						<label>Dimensions</label>		
						<input type="text" id="dialog-width-input" size="5"/> &#215; <input type="text" id="dialog-height-input" size="5"/> pixels</p>
					</p>
					<p class="dialog-form-line">
						<input type="hidden" id="dialog-player-default" value="<?php echo $options['player']; ?>"/>
						<label>Player choisi</label>		
						<select id="dialog-player">
							<?php 
								foreach( $aPlayers as $player ){
									$selected = "";
									if( $options['player'] == $player->iPlayer ){
										$selected = 'selected="selected"';
									}
									echo "<option value='".$player->iPlayer."' $selected>".ucfirst($player->sName)."</option>";
								}
							?>
						</select>
					</p>
					<p class="dialog-form-line">
						<label>Etiré la video (stretch)</label>
						<input type="checkbox" id="dialog-stretch" checked="checked" value="1"/>
					</p>
					<p class="dialog-form-line">
						<label>Démarrage automatique</label>
						<input type="checkbox" id="dialog-autostart" value="1"/>
					<p>
					<p class="dialog-form-line">
						<label>Lecture en boucle</label>
						<input type="checkbox" id="dialog-loop" value="1"/>
					</p>
				</div>
			</div>
		</div>
	</div>
	<?php
	}

	// WordPress' js_escape() won't allow <, >, or " -- instead it converts it to an HTML entity. This is a "fixed" function that's used when needed.
	static function js_escape($text) {
		$safe_text = addslashes($text);
		$safe_text = preg_replace('/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes($safe_text));
		$safe_text = preg_replace("/\r?\n/", "\\n", addslashes($safe_text));
		$safe_text = str_replace('\\\n', '\n', $safe_text);
		return apply_filters('js_escape', $safe_text, $text);
	}

	static function adminMenu( $action_url, $options, $sUrl){
		?>
		<h2>Administration du plugin VOD</h2>
		<form name="adminForm" action="<?php echo $action_url; ?>" method="post">
			<input type="hidden" name="submitted" value="1" />
			<p>
				Pour fonctionner, le plugin à besoin de s'interfacer avec votre compte VOD infomaniak.<br/>
				Pour des raisons de sécurités, il est fortement conseillé de créer un nouvel utilisateur dédié dans votre admin infomaniak avec uniquement des droits restreints sur l'API.<br/>
				Pour plus d'information, veuillez vous rendre dans la partie "Configuration > API et Callback" de votre administration VOD.
			</p>
			<p>
				<label>Login :</label>
				<input type="text" id="vod_api_login" name="vod_api_login" value="<?php echo $options['vod_api_login']; ?>"/>
			</p>
			<p>
				<label>Password :</label>
				<input type="password" id="vod_api_password" name="vod_api_password" value="XXXXXX"/>
			</p>
			<p>
				<label>Identifiant du compte :</label>
				<input type="text" id="vod_api_id" name="vod_api_id" value="<?php echo $options['vod_api_id']; ?>"/>
			</p>
			<p>
				<label>Connection :</label>
				<?php 
					if( $options['vod_api_connected'] == "on") {
						echo "<span style='color: green;'>Connecter</span>";
					} else {
						echo "<span style='color: red;'>Impossible de se connecter</span>";
					}
				?>
			</p>
			<div class="submit"><input type="submit" name="Submit" value="Update options" /></div>
		</form>

		<?php 
			if( $options['vod_api_connected'] == "on") {
				?>
				<h2>Synchronisation des données</h2>
				<p>
					Cette extension vous permet de recuperer facilement les players et les dossiers préalablement configurés sur votre compte.<br/>
					Pour cela il est nécéssaire de synchroniser les données entre ce site et votre compte.<br/>
					Cette opération est automatique mais peut prendre du temps, il peut etre nécéssaire de la faire manuellement.
				</p>
				<p>
					<label>Vidéos récupérés :</label>
					<span style="font-weight: bold;"><?php echo intval($options['vod_count_video']); ?></span>
				</p>
				<p>
					<label>Dossiers récupérés :</label>
					<span style="font-weight: bold;"><?php echo intval($options['vod_count_folder']); ?></span>
				</p>
				<p>
					<label>Players récupérés :</label>
					<span style="font-weight: bold;"><?php echo intval($options['vod_count_player']); ?></span>
				</p>
				<p>
					<label>Playlist récupérés :</label>
					<span style="font-weight: bold;"><?php echo intval($options['vod_count_playlist']); ?></span>
				</p>


				<div class="submit">
					<form id="updateSynchro" name="updateSynchro" action="<?php echo $action_url; ?>" method="post">
						<input type="hidden" name="updateSynchro" value="1" /> 
						<input type="submit" name="Submit" value="Synchronisation rapide" />
					</form>
					<form id="updateSynchroVideo" name="updateSynchroVideo" action="<?php echo $action_url; ?>" method="post">
						<input type="hidden" name="updateSynchroVideo" value="1" /> 
						<input type="submit" name="Submit" value="Synchroniser Vidéos" />
					</form>
				</div>

				<h2>Configuration du callback</h2>
				<p>
				Cette option vous permet de mettre à jour automatiquement votre blog à chaque ajout de vidéo à votre espace VOD.<br/>
				Veuillez aller dans "Configuration -> Api & Callback" et mettre l'adresse suivante dans le champ "Adresse de Callback"
				</p>
				<!--<?php if( $this->options['vod_api_valid_callback'] == "on" ){ ?>
					<p>
						Une autre adresse de callback est défini, celui-ci ne fonctionnera donc pas avec ce blog.
					</p>
				<?php } ?>-->
				<p>
					<label>Adresse à saisir : </label>
					<span><?php echo $sUrl."/?vod_page=callback&key=".$options['vod_api_callbackKey']; ?></span>
				</p>
				<?php
			}
	}

	static function tabLastUpload( $aLastImport ){
		$sTab = "";		
		if( !empty( $aLastImport ) ){
			$sTab .= "<span id='tabImportRefresh' style='float:right; padding-right: 20px;'></span>";
			$sTab .= "<h2>Précédents Envois</h2>";
			$sTab .= "<table class='widefat'><thead><tr>";
			$sTab .= "<th>Fichier</th><th>Date</th><th>Statut</th><th>Description</th>";
			$sTab .= "</tr></thead><tbody>";
			foreach( $aLastImport as $oImport ){ 
				$sTab .= "<tr>";
				$sTab .= " <td><img src='" . plugins_url('vod-infomaniak/img/videofile.png') . "'/>". $oImport['sFileName'] ."</td>";
				$sTab .= " <td>". $oImport['dDateCreation'] ."</td>";
				$sTab .= " <td>";
				if( $oImport['sProcessState'] == "OK" ){
					$sTab .= " <img src='" . plugins_url('vod-infomaniak/img/ico-tick.png') . "'/> Ok";

				}else if( $oImport['sProcessState'] == "WARNING"){
					$sTab .= "<img src='" . plugins_url('vod-infomaniak/img/videofile.png') . "'/> Ok (des alertes sont apparues)";

				}else if( $oImport['sProcessState'] == "DOWNLOAD"){
					$sTab .= "<img src='" . plugins_url('vod-infomaniak/img/ico-download.png') . "'/> Téléchargement en cours";

				}else if( $oImport['sProcessState'] == 'WAITING' || $oImport['sProcessState'] == 'QUEUE' || $oImport['sProcessState'] == 'PROCESSING'){
					$sTab .= "<img src='" . plugins_url('vod-infomaniak/img/ajax-loader.gif') . "'/> En cours de conversion";

				}else{
					$sTab .= "<img src='" . plugins_url('vod-infomaniak/img/ico-exclamation-yellow.png') . "'/> Erreurs";
				}
				$sTab .= " </td>";
				$sTab .= " <td width='50%'>". $oImport['sLog'] ."</td>";
				$sTab .= "</tr>";
			}
			$sTab .= "</tbody></table>";
		 }
		return $sTab;
	}

	static function uploadMenu( $actionurl, $options, $aFolders, $sTab=""){
		?>
		<h2>Envoi d'une nouvelle vidéo</h2>
		<p>
			Ce plug-in vous permet d'ajouter de nouvelles vidéos directement depuis ce blog. Pour cela, vous n'avez qu'à choisir un dossier puis suivre les :
		</p>		
		<p>
			<label><b>1.</b> Choix du dossier d'envoi:</label><br/>
			<select id="uploadSelectFolder" onchange="changeFolder();" onkeyup="changeFolder();">
				<option value="-1" selected="selected">-- Dossier d'envoi --</option>
			<?php 
				foreach( $aFolders as $oFolder ){
					echo "<option value='".$oFolder->iFolder."'>Dossier : /".$oFolder->sPath." , Nom : ".$oFolder->sName."</option>";
				}
			?>
			</select>
		</p>
		<p>
			<div id="submitLine" class="submit">
				<label><b>2.</b> Choix du type d'envoi :</label><br/>
				<input type="button" name="Submit" value="Envoie depuis cet ordinateur" onclick="vod_uploadPopup();"/>
				<input type="button" name="Submit" value="Importer depuis un autre site" onclick="vod_importPopup();"/>
			</div>
		</p>

		
		<div id="tabImport"><?php echo $sTab; ?></div>

		<script type="text/javascript">
			changeFolder = function(){
				if( jQuery("#uploadSelectFolder").val() != -1 ){
					jQuery("#submitLine").show();
				}else{
					jQuery("#submitLine").hide();
				}
			};
			changeFolder();

			vod_uploadPopup = function(){
				var height = 550;
				var width = 1024;
				var top=(screen.height - height)/2;
				var left=(screen.width - width)/2;
				window.open('<?php echo $actionurl; ?>&sAction=popupUpload&iFolderCode='+jQuery("#uploadSelectFolder").val(), 'UploadTool'+jQuery("#uploadSelectFolder").val(), 
					config='height='+height+', width='+width+', top='+top+', left='+left+', toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no'
				);
			};

			vod_importPopup = function(){
				var height = 550;
				var width = 1024;
				var top=(screen.height - height)/2;
				var left=(screen.width - width)/2;
				window.open('<?php echo $actionurl; ?>&sAction=popupImport&iFolderCode='+jQuery("#uploadSelectFolder").val(), 'importTool'+jQuery("#uploadSelectFolder").val(), 
					config='height='+height+', width='+width+', top='+top+', left='+left+', toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no'
				);
			};

			(function(){
				iAjaxRefresh = 5;
				iAjaxDecompte = 0;

				jQuery(document).ready(function() {
					iAjaxDecompte = 30;
					if( jQuery('#tabImportRefresh') && jQuery('#tabImport') ){
						setTimeout('update_vod_import();', 30000);
						setTimeout('update_info();', 100);
					}
				});

				update_info = function(){
					if( iAjaxDecompte >= 0 ){
						iAjaxDecompte -= 1;
						jQuery('#tabImportRefresh').html("<span style='font-style:italic;color: #666666;'><img src='<?php echo plugins_url('vod-infomaniak/img/ico-refresh.png'); ?>' style='vertical-align:bottom;'/> Mise à jour dans "+ (iAjaxDecompte*1+1) +" secondes</span>");
					}
					setTimeout('update_info();', 1000);
				}

				update_vod_import = function() { 
					iAjaxDecompte = 0;
					jQuery.ajax({
						type: "post",url: "admin-ajax.php",data: { action: 'importvod'},
						success: function(html){
							jQuery("#tabImport").html(html);
						}
					});
					if( iAjaxRefresh < 10 ){
						iAjaxDecompte = 30;
						setTimeout('update_vod_import();', 30000);
					}else if( iAjaxRefresh < 25 ){
						iAjaxDecompte = 60;
						setTimeout('update_vod_import();', 60000);
					}else if( iAjaxRefresh < 30 ){
						iAjaxDecompte = 120;
						setTimeout('update_vod_import();', 120000);
					}else if( iAjaxRefresh < 40 ){
						iAjaxDecompte = 300;
						setTimeout('update_vod_import();', 300000);
					}else if( iAjaxRefresh < 50 ){
						iAjaxDecompte = 600;
						setTimeout('update_vod_import();', 600000);
					}else{
						iAjaxDecompte = -1;
					}
					iAjaxRefresh++;
				}

				uploadFinish = function(){
					iAjaxRefresh = 0;
					update_vod_import();
				}

			})();

		</script>
		<?php
	}
	
	static function uploadPopup( $token, $oFolder ){
		?>
		<script type="text/javascript" charset="iso-8859-1" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.1/swfobject.js" ></script>
		<script type="text/javascript" charset="iso-8859-1" src="http://vod.infomaniak.com/apiUpload/flashUpload.js" ></script>
		<h2>Utilitaire d'envoi de vidéo</h2>

		<p>
			<label style="font-weight: bold">Dossier d'envoi :</label>
			<span><img src="<?php echo plugins_url('vod-infomaniak/img/ico-folder-open-16x16.png'); ?>" style="vertical-align:bottom"/> <?php echo $oFolder->sName; ?> ( '<?php echo $oFolder->sPath; ?>' )</span>
		</p>
		<p>
			<label style="font-weight: bold">Limites :</label>
			<ul style="list-style: disc inside; margin-left: 20px;">
				<li>Le poids des fichiers envoyés via ce module est limité à 1Go</li>
				<li>Les formats vidéos supportés sont avi, flv, mov, mpeg, mp4, mkv, rm, wmv, m4v, vob, 3gp, webm, f4v, ts</li>
				<li>L'envoi doit être effectué en moins de 4 heures</li>
			</ul>
		</p>
		<p>
			<label style="font-weight: bold">Envoi :</label>
		</p>
		<div id="up"></div>
		
			
		<script type="text/javascript">
			jQuery('#adminmenuwrap').remove();
			flashUpload('<?php echo $token; ?>');

			multiUploadCallback = function(json){
				oJson = eval('('+json+')');
				switch (oJson.sStatus){
					case "init":
						document.getElementById('up').callbackInitialisation();
						break;
					case "complete":
						document.getElementById('up').callbackProcessing(oJson.iCurrent,true);
						setTimeout('CallParentWindowFunction();', 2500);
						break;
					case "error":
						alert('upload error : '+oJson.sOriginalFileName);
						break;
				}
			}

			CallParentWindowFunction = function(){
				window.opener.uploadFinish();
				return false;
			}
		</script>

		<?php
	}

	static function importPopup( $action_url, $oFolder, $bResult ){
		?>
		<h2>Utilitaire d'importation de vidéo</h2>
		
		<form name="adminForm" action="<?php echo $action_url; ?>" method="post">
			<input type="hidden" name="submit" value="1"/>
			<input type="hidden" name="sAction" value="popupImport"/>
			<input type="hidden" name="iFolder" value="<?php echo $oFolder->iFolder; ?>"/>
			<p>
				<label style="font-weight: bold">Dossier d'envoi :</label>
				<span><img src="<?php echo plugins_url('vod-infomaniak/img/ico-folder-open-16x16.png'); ?>" style="vertical-align:bottom"/> <?php echo $oFolder->sName; ?> ( '<?php echo $oFolder->sPath; ?>' )</span>
			</p>
			<p>
				<label style="font-weight: bold">Limites :</label>
				<ul style="list-style: disc inside; margin-left: 20px;">
					<li>Le poids des fichiers envoyés via ce module est limité à 1Go</li>
					<li>Les formats vidéos supportés sont avi, flv, mov, mpeg, mp4, mkv, rm, wmv, m4v, vob, 3gp, webm, f4v, ts</li>
				</ul>
			</p>
			<p>
				<label style="font-weight: bold">Adresse :</label>
				<select name="sProtocole" id="sProtocole">
					<option value="http">http://</option>
					<option value="https">https://</option>
					<option value="ftp">ftp://</option>
				</select>
				<input type="text" onkeyup="checkURL();" showsuccess="false" style="width: 50%" value="" name="sUrl" id="sUrl">
			</p>
			<p>
				<input type="checkbox" value="1" onclick="checkAuth();" name="bNeedAuth" id="bNeedAuth">
				Cette adresse nécessite une authentification.
			</p>
			<p id="authLine">
				<label style="font-weight: bold">Login :</label> <input type="text" name="sLogin">
				<label style="font-weight: bold">Password :</label> <input type="password" name="sPassword">
			</p>
			<div class="submit"><input type="submit" name="Submit" value="Importer" /></div>
		</form>
		<script type="text/javascript">
			jQuery('#adminmenuwrap').remove();
			
			checkURL = function(){
				var url = jQuery('#sUrl').val();
				if (url.indexOf("http://") !=-1) {
					jQuery('#sProtocole').val('http');
					jQuery('#sUrl').val( url.replace(/http:\/\//i, "") );
				}else if (url.indexOf("https://") !=-1) {
					jQuery('#sProtocole').val('https');
					jQuery('#sUrl').val( url.replace(/https:\/\//i, "") );
				}else if (url.indexOf("ftp://") !=-1) {
					jQuery('#sProtocole').val('ftp');
					jQuery('#sUrl').val( url.replace(/ftp:\/\//i, "") );
				}
			};

			checkAuth = function(){
				if( jQuery("#bNeedAuth").attr('checked') ){
					jQuery("#authLine").show();
				}else{
					jQuery("#authLine").hide();
				}
			};
			checkAuth();

			CallParentWindowFunction = function(){
				window.opener.uploadFinish();
				return false;
			}
			<?php if($bResult){ echo "CallParentWindowFunction();"; } ?>
		</script>
		<?php
	}

	static function managementMenu( $sPagination, $aOptions, $aVideos){
		?>
		<h2>Gestionnaire de vidéos</h2>

		<div class="tablenav">
			<div class='tablenav-pages'>
				<?php echo $sPagination; ?>
			</div>
		</div>

		<div id="dialog-modal-vod" title="Prévisualisation" style="display:none; padding: 5px; overflow: hidden;">
			<h3 id="dialog-modal-title" style="text-align:center; margin: 5px">Titre</h3>
			<center>
				<iframe id="dialog-modal-video" frameborder="0" width="480" height="320" src="#"></iframe>
			</center>
			<h3>Informations</h3>
			<p>
				<input id="dialog-modal-name" text="" style="float:right; margin-right:25px; width: 400px;"/>				
				<label>Nom :</label>	
			</p>			
			<h3>Intégration</h3>
			<p>
				<input id="dialog-modal-url" text="" style="float:right; margin-right:25px; width: 400px; border 1px solid #CCC; border-radius: 3px; background-color: #FFF; padding: 3px;" readonly="value"/>
				<label>Url de la vidéo :</label>				
			</p>
			<p>
				<input id="dialog-modal-balise" text="" style="float:right; margin-right:25px; width: 400px; border 1px solid #CCC; border-radius: 3px; background-color: #FFF; padding: 3px;" readonly="value"/>
				<label>Code d'intégration :</label>
			</p>
			<p>
				<label>Edition de la vidéo :</label> <span style="padding-left: 30px;"><a id="dialog-modal-href" href="#" target="_blank">Accès à l'admin VOD</a></span>
			</p>
		</div>

		<table class="widefat">
			<thead>
				<tr>
					<th>Video</th>
					<th>Dossier</th>
					<th>Date d'upload</th>
					<th>Action</th>			
				</tr>
			</thead>
			<tbody>
				<?php
					foreach( $aVideos as $oVideo ){
				?>
				<tr>
					<td>
						<img src="<?php echo plugins_url('vod-infomaniak/img/videofile.png'); ?>"/>
						<a href="javascript:; return false;" onclick="openVodPopup('<?php echo $oVideo->iVideo; ?>', '<?php echo $oVideo->sName; ?>','<?php echo $oVideo->sPath.$oVideo->sServerCode."', '".strtolower($oVideo->sExtension);?>'); return false;"><?php echo $oVideo->sName; ?></a>
					</td>
					<td><img src="<?php echo plugins_url('vod-infomaniak/img/ico-folder-open-16x16.png'); ?>"/> <?php echo $oVideo->sPath; ?></td>
					<td><?php echo $oVideo->dUpload; ?></td>
					<td> </td>
				</tr>
				<?php	} ?>
			</tbody>
			<script>
				openVodPopup = function( iVideo, title, url, sExtension ){
					var urlComplete = "<?php echo $aOptions['vod_api_id'];?>"+url;
					jQuery( "#dialog-modal-title").text( title );
					jQuery( "#dialog-modal-name").val( title );
					jQuery( "#dialog-modal-url").val( "http://vod.infomaniak.com/redirect/"+urlComplete+"."+sExtension );
					jQuery( "#dialog-modal-balise").val( "[vod]"+url+"."+sExtension+"[/vod]" );
					jQuery( "#dialog-modal-href").attr( "href", "https://statslive.infomaniak.com/vod/videoDetail.php?iVodCode=<?php echo $aOptions['vod_api_icodeservice'];?>&iFileCode="+iVideo );
					jQuery( "#dialog-modal-video").attr( "src", "http://vod.infomaniak.com/iframe.php?url="+urlComplete+"."+sExtension+"&player=576&vod=214&preloadImage="+urlComplete+".jpg" );

					jQuery( "#dialog-modal-vod").dialog({
						width: 600,
						height: 620,
						//modal: true,
						resizable: false,
						beforeClose: function(event, ui) {
							jQuery( "#dialog-modal-video").attr( "src", "#");
						}
					});
					return false;
				}
			</script>
		</table>
		<?php
	}

	static function playlistMenu($actionurl, $options, $aPlaylist){
		?>
		<h2>Playlists</h2>
		<p>
			Si vous souhaitez ajouter ou modifier les playlist ci-dessous, veuillez vous rendre dans <a href="https://statslive.infomaniak.com/vod/playlists.php?iVodCode=<?php echo $options['vod_api_icodeservice'];?>" target="_blank">la console d'administration</a>
		</p>

		<h2>Précédents Envois</h2>
			<table class='widefat'>
				<thead>
					<tr>
						<th>Nom</th>
						<th>Description</th>
						<th>Nombre vidéos</th>
						<th>Mode de lecture</th>
						<th>Date</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach( $aPlaylist as $oPlaylist ){ ?>
					<tr>
						<td><?php echo $oPlaylist->sPlaylistName; ?></td>
						<td><?php echo $oPlaylist->sPlaylistDescription; ?></td>
						<td><?php echo $oPlaylist->iTotal; ?></td>
						<td><?php echo $oPlaylist->sMode; ?></td>
						<td><?php echo $oPlaylist->dCreated; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>

		<?php
	}

	static function implementationMenu( $actionurl, $options, $aPlayers ){
		?>
		<h2>Intégration par défaut des vidéos</h2>
		<form name="adminForm" action="<?php echo $action_url; ?>" method="post">
			<input type="hidden" name="submitted" value="1" /> 
			<p>
				<label>Selection du player par défaut :</label>
				<select id="selectPlayer" name="selectPlayer" onchange="PlayerInfo();" onkeyup="PlayerInfo();">
				<?php 
					foreach( $aPlayers as $player ){
						$selected = "";
						if( $options['player'] == $player->iPlayer ){
							$selected = 'selected="selected"';
						}
						echo "<option value='".$player->iPlayer."' $selected>".ucfirst($player->sName)."</option>";
					}
				?>
				</select>
				
				<p>Informations sur ce Player :</p>
				<?php foreach( $aPlayers as $player ){ ?>
					<div id="player-info-<?php echo ucfirst($player->iPlayer); ?>" class="player-info" style="padding: 5px 15px; border: 1px solid #EEE; display:none; width: 500px;">
						
						<ul>
							<li><b>Nom :</b> <?php echo ucfirst($player->sName); ?></li>
							<li><b>Date :</b> <?php echo date("d M Y H:i", strtotime($player->dEdit)); ?></li>
							<li><b>Résolution :</b> <?php echo $player->iWidth; ?>x<?php echo $player->iHeight; ?></li>
							<li><b>Démarrage automatique :</b> <?php echo $player->bAutoPlay==0? 'Non': 'Oui'; ?></li>
							<li><b>Lecture en boucle :</b> <?php echo $player->bLoop==0? 'Non': 'Oui'; ?></li>
							<li><b>Switch de qualité :</b> <?php echo $player->bSwitchQuality==0? 'Non': 'Oui'; ?></li>
						</ul>
					</div>
				<?php } ?>
				
			</p>
			<div class="submit">
				<input type="submit" name="Submit" value="Choisir ce player" />
			</div>
		</form>
		<h2>Création ou modification de players</h2>
		<p>
			Afin de modifier ou créer de nouveaux players flash, nous vous invitons à vous rendre dans votre administration vod : <a href="https://statslive.infomaniak.com/vod/player.php?iVodCode=<?php echo $options['vod_api_icodeservice'];?>" target="_blank">Accèder à la configuration des players</a>
		</p>
		<script>
			PlayerInfo = function(){
				jQuery('.player-info').hide();
				value = jQuery('#selectPlayer').val();
				console.log(value);
				jQuery('#player-info-'+value).show();
			}
			PlayerInfo();
		</script>
		<?php
	}

	static function buildPagination( $iCurrentPage, $iLimit, $iTotal ){
		$iTotalPage = $iTotal;
		$iPageTotal = floor(($iTotal-1) / $iLimit) + 1; 
 		
		if (($iCurrentPage != 1) && ($iCurrentPage)) {
			$page_list .= "  <a href=\" ".$_SERVER['PHP_SELF']."?page=vod-infomaniak/vod.class.php&p=1\" title=\"First Page\">«</a> ";
		} 

		if (($iCurrentPage-1) > 0) {
			$page_list .= "<a href=\" ".$_SERVER['PHP_SELF']."?page=vod-infomaniak/vod.class.php&p=".($iCurrentPage-1)."\" title=\"Previous Page\"><</a> ";
		} 
		
		for ($i=1; $i<=$iPageTotal; $i++) {
			if( $i <= 2 || $i > $iPageTotal-2 || ($i>=$iCurrentPage -2 && $i<=$iCurrentPage+2 ) ){
				if ($i == $iCurrentPage) {
					$page_list .= "<b>".$i."</b>";
				} else {
					$page_list .= "<a href=\" ".$_SERVER['PHP_SELF']."?page=vod-infomaniak/vod.class.php&p=".$i."\" title=\"Page ".$i."\">".$i."</a>";
				} 
				$page_list .= " ";
			}else if ( $i == 3 || $i == $iPageTotal-2 ){
				$page_list .= "... ";
			}
		} 

		if (($iCurrentPage+1) <= $iPageTotal) {
			$page_list .= "<a href=\"".$_SERVER['PHP_SELF']."?page=vod-infomaniak/vod.class.php&p=".($iCurrentPage+1)."\" title=\"Next Page\">></a> ";
		} 

		if (($iCurrentPage != $iPageTotal) && ($iPageTotal != 0)) {
			$page_list .= "<a href=\"".$_SERVER['PHP_SELF']."?page=vod-infomaniak/vod.class.php&p=".$iPageTotal."\" title=\"Last Page\">»</a> ";
		}
		$page_list .= "</td>\n"; 
		
		return $page_list;
	}
}
?>
