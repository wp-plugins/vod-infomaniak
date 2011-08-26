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

	static function buildForm( $options, $aPlayers ) { 
	?>
	<div class="hidden">
		<div id="dialog-vod-form">
			<div id="dialog-tabs" class="ui-tabs">
				<ul class="ui-tabs-nav">
					<li><a href="#dialog-tab1"><?php _e("Avec l'url",'vod_infomaniak');?></a></li>
					<li><a href="#dialog-tab2"><?php _e('Outil de recherche','vod_infomaniak');?></a></li>
				</ul>
				<div id="dialog-tab1" class="ui-tabs-panel">
					<div style="padding-left: 20px; padding-bottom: 10px;"><?php _e("Veuillez saisir l'URL d'une video",'vod_infomaniak');?></div>
					<div style="padding-left: 20px;">
						<strong><?php _e('Exemple','vod_infomaniak');?> :</strong>
						<ul id="dialog-exemple">
							<li><?php _e('Url complete','vod_infomaniak');?> : <code>http://vod.infomaniak.com/redirect/infomaniak_vod1/folder-234/mp4-148/video.mp4</code>
							<li><?php _e('Url partiel','vod_infomaniak');?> : <code>folder-234/mp4-148/video.mp4</code></li>
							<li><?php _e('Identifiant de playlist','vod_infomaniak');?> : <code>25</code></li>
						</ul>
					</div>
					<p style="text-align:center"><input type="text" id="dialog-url-input"/></p>
				</div>
				<div id="dialog-tab2" class="ui-tabs-panel">
					<input type="hidden" id="url_ajax_search_video" value="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php?action=vodsearchvideo"/>
					<input type="hidden" id="url_ajax_search_playlist" value="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php?action=vodsearchplaylist"/>
					<div style="padding-left: 30px;">
						<label><?php _e("Recherche d'une",'vod_infomaniak');?> :</label><br/>
						<input type="radio" name="searchtype" id="video" value="video" checked="checked" onclick="checkSearchType();"> <label for="video"><?php _e('Video','vod_infomaniak');?></label>
						<input type="radio" name="searchtype" id="playlist" value="playlist" onclick="checkSearchType();"> <label for="playlist"><?php _e('Playlist','vod_infomaniak');?></label>
						<br/>
					</div>
					<p style="text-align:center">
						<input id="dialog-search-input-video" class="dialog-search-input"/>
						<input id="dialog-search-input-playlist" class="dialog-search-input"/>
					</p>
				</div>
			</div>
			<div id="dialog-config">
				<div id="dialog-slide-header" class="ui-dialog-titlebar" onclick="Vod_dialogToggleSlider();"><?php _e('Options d\'integration','vod_infomaniak');?></div>
				<div id="dialog-slide" style="display:none">
					<p class="dialog-form-line">
						<label><?php _e('Dimensions','vod_infomaniak');?></label>		
						<input type="text" id="dialog-width-input" size="5"/> &#215; <input type="text" id="dialog-height-input" size="5"/> pixels</p>
					</p>
					<p class="dialog-form-line">
						<input type="hidden" id="dialog-player-default" value="<?php echo $options['player']; ?>"/>
						<label><?php _e('Player choisi','vod_infomaniak');?></label>		
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
						<label><?php _e('Etirer la video (stretch)','vod_infomaniak');?></label>
						<input type="checkbox" id="dialog-stretch" checked="checked" value="1"/>
					</p>
					<p class="dialog-form-line">
						<label><?php _e('Demarrage automatique','vod_infomaniak');?></label>
						<input type="checkbox" id="dialog-autostart" value="1"/>
					<p>
					<p class="dialog-form-line">
						<label><?php _e('Lecture en boucle','vod_infomaniak');?></label>
						<input type="checkbox" id="dialog-loop" value="1"/>
					</p>
					<p class="dialog-form-line">
						<label><?php _e('Utilisation du token du dossier (Id dossier)','vod_infomaniak');?></label>
						<input type="text" id="dialog-token" size="5"/>
					</p>
				</div>
			</div>
		</div>
	</div>
	<?php
	}

	static function adminMenu( $action_url, $options, $sUrl){
		?>
		<h2><?php _e('Administration du plugin VOD','vod_infomaniak');?></h2>
		<form name="adminForm" action="<?php echo $action_url; ?>" method="post">
			<input type="hidden" name="submitted" value="1" />
			<p>
				<?php _e("Pour fonctionner, le plugin a besoin de s'interfacer avec votre compte VOD infomaniak.<br/>
				Pour des raisons de securites, il est fortement conseille de creer un nouvel utilisateur dedie dans votre admin infomaniak avec uniquement des droits restreints sur l'API.<br/>
				Pour plus d'information, veuillez vous rendre dans la partie \"Configuration -> Api & Callback\" de votre administration VOD.",'vod_infomaniak');?>
				
			</p>
			<p>
				<label><?php _e('Login','vod_infomaniak'); ?> :</label>
				<input type="text" id="vod_api_login" name="vod_api_login" value="<?php echo $options['vod_api_login']; ?>"/>
			</p>
			<p>
				<label><?php _e('Password','vod_infomaniak'); ?> :</label>
				<input type="password" id="vod_api_password" name="vod_api_password" value="XXXXXX"/>
			</p>
			<p>
				<label><?php _e('Identifiant du compte','vod_infomaniak'); ?> :</label>
				<input type="text" id="vod_api_id" name="vod_api_id" value="<?php echo $options['vod_api_id']; ?>"/>
			</p>
			<p>
				<label><?php _e('Connection','vod_infomaniak'); ?> :</label>
				<?php 
					if( $options['vod_api_connected'] == "on") {
						echo "<span style='color: green;'>".__('Connecter','vod_infomaniak');"</span>";
					} else {
						echo "<span style='color: red;'>".__('Impossible de se connecter','vod_infomaniak');"</span>";
					}
				?>
			</p>
			<div class="submit"><input type="submit" name="Submit" value="<?php _e('Valider','vod_infomaniak'); ?>" /></div>
		</form>

		<?php 
			if( $options['vod_api_connected'] == "on") {
				?>
				<h2><?php _e('Synchronisation des donnees','vod_infomaniak'); ?></h2>
				<p><?php _e("Pour fonctionner correctement, cette extension a besoin de se synchroniser regulierement avec votre compte VOD.<br/>Cela vous permet de garder une liste des players, dossiers et playlist a jour sur votre blog.<br/>Cette operation s'effectue automatiquement assez regulierement mais il est egalement possible de forcer une verification ci-dessous.",'vod_infomaniak'); ?></p>
				<p>
					<label><?php _e('Videos recuperes','vod_infomaniak'); ?> :</label>
					<span style="font-weight: bold;"><?php echo intval($options['vod_count_video']); ?></span>
				</p>
				<p>
					<label><?php _e('Dossiers recuperes','vod_infomaniak'); ?> :</label>
					<span style="font-weight: bold;"><?php echo intval($options['vod_count_folder']); ?></span>
				</p>
				<p>
					<label><?php _e('Players recuperes','vod_infomaniak'); ?> :</label>
					<span style="font-weight: bold;"><?php echo intval($options['vod_count_player']); ?></span>
				</p>
				<p>
					<label><?php _e('Playlist recuperes','vod_infomaniak'); ?> :</label>
					<span style="font-weight: bold;"><?php echo intval($options['vod_count_playlist']); ?></span>
				</p>


				<div class="submit">
					<form id="updateSynchro" name="updateSynchro" action="<?php echo $action_url; ?>" method="post" style="display:inline;">
						<input type="hidden" name="updateSynchro" value="1" /> 
						<input type="submit" name="Submit" value="<?php _e('Synchronisation rapide','vod_infomaniak'); ?>" />
					</form>
					<form id="updateSynchroVideo" name="updateSynchroVideo" action="<?php echo $action_url; ?>" method="post" style="display:inline;">
						<input type="hidden" name="updateSynchroVideo" value="1" /> 
						<input type="submit" name="Submit" value="<?php _e('Synchroniser Videos','vod_infomaniak'); ?>" />
					</form>
				</div>

				<h2><?php _e('Configuration du callback','vod_infomaniak'); ?></h2>
				<p><?php printf( __("Cette option vous permet de mettre a jour automatiquement votre blog a chaque ajout de video a votre espace VOD.<br/>Veuillez aller dans \"<a href='https://statslive.infomaniak.com/vod/configuration.php?iVodCode=%d' target='_blank'>Configuration -> Api & Callback</a>\" et mettre l'adresse suivante dans le champ \"Adresse de Callback\"",'vod_infomaniak'), $options['vod_api_icodeservice']); ?>
				
				</p>
				<p>
					<label style="font-weight: bold;"><?php _e('Adresse de callback','vod_infomaniak'); ?> :</label>
					<span><?php echo $sUrl."/?vod_page=callback&key=".$options['vod_api_callbackKey']; ?></span>
				</p>
				<?php
			}
	}

	static function tabLastUpload( $aLastImport ){
		$sTab = "";		
		if( !empty( $aLastImport ) ){
			$sTab .= "<span id='tabImportRefresh' style='float:right; padding-right: 20px;'></span>";
			$sTab .= "<h2>".__('Precedents Envois','vod_infomaniak')."</h2>";
			$sTab .= "<table class='widefat' style='width: 99%'><thead><tr>";
			$sTab .= "<th>".__('Fichier','vod_infomaniak')."</th><th>".__('Date','vod_infomaniak')."</th><th>".__('Statut','vod_infomaniak')."</th><th>".__('Description','vod_infomaniak')."</th>";
			$sTab .= "</tr></thead><tbody>";
			foreach( $aLastImport as $oImport ){ 
				$sTab .= "<tr>";
				$sTab .= " <td><img src='" . plugins_url('vod-infomaniak/img/videofile.png') . "' style='vertical-align:bottom'/>". $oImport['sFileName'] ."</td>";
				$sTab .= " <td>". $oImport['dDateCreation'] ."</td>";
				$sTab .= " <td>";
				if( $oImport['sProcessState'] == "OK" ){
					$sTab .= " <img src='" . plugins_url('vod-infomaniak/img/ico-tick.png') . "' style='vertical-align:bottom'/> ".__('OK','vod_infomaniak');

				}else if( $oImport['sProcessState'] == "WARNING"){
					$sTab .= "<img src='" . plugins_url('vod-infomaniak/img/videofile.png') . "' style='vertical-align:bottom'/> ".__('Ok (des alertes sont apparues)','vod_infomaniak');

				}else if( $oImport['sProcessState'] == "DOWNLOAD"){
					$sTab .= "<img src='" . plugins_url('vod-infomaniak/img/ico-download.png') . "' style='vertical-align:bottom'/> ".__('Telechargement en cours','vod_infomaniak');

				}else if( $oImport['sProcessState'] == 'WAITING' || $oImport['sProcessState'] == 'QUEUE' || $oImport['sProcessState'] == 'PROCESSING'){
					$sTab .= "<img src='" . plugins_url('vod-infomaniak/img/ajax-loader.gif') . "' style='vertical-align:bottom'/> ".__('En cours de conversion','vod_infomaniak');

				}else{
					$sTab .= "<img src='" . plugins_url('vod-infomaniak/img/ico-exclamation-yellow.png') . "' style='vertical-align:bottom'/> ".__('Erreurs','vod_infomaniak');
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
		<h2><?php _e("Envoi d'une nouvelle video",'vod_infomaniak'); ?></h2>
		<p><?php _e("Ce plug-in vous permet d'ajouter de nouvelles videos directement depuis ce blog. Pour cela, vous n'avez qu'a choisir un dossier puis suivre les instructions",'vod_infomaniak'); ?> :</p>		
		<p>
			<label><b>1.</b> <?php _e("Choix du dossier d'envoi",'vod_infomaniak'); ?> :</label><br/>
			<select id="uploadSelectFolder" onchange="changeFolder();" onkeyup="changeFolder();">
				<option value="-1" selected="selected">-- Dossier d'envoi --</option>
			<?php 
				foreach( $aFolders as $oFolder ){
					echo "<option value='".$oFolder->iFolder."'>".__('Dossier','vod_infomaniak')." : /".$oFolder->sPath." , ".__('Nom','vod_infomaniak')." : ".$oFolder->sName."</option>";
				}
			?>
			</select>
		</p>
		<p>
			<div id="submitLine" class="submit">
				<label><b>2.</b> <?php _e("Choix du type d'envoi",'vod_infomaniak'); ?> :</label><br/>
				<input type="button" name="Submit" value="<?php _e("Envoyer depuis cet ordinateur",'vod_infomaniak'); ?>" onclick="vod_uploadPopup();"/>
				<input type="button" name="Submit" value="<?php _e("Importer depuis un autre site",'vod_infomaniak'); ?>" onclick="vod_importPopup();"/>
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
						jQuery('#tabImportRefresh').html("<span style='font-style:italic;color: #666666;'><img src='<?php echo plugins_url('vod-infomaniak/img/ico-refresh.png'); ?>' style='vertical-align:bottom;'/> <?php _e("Mise a jour dans",'vod_infomaniak'); ?> "+ (iAjaxDecompte*1+1) +" secondes</span>");
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
		<h2><?php _e("Utilitaire d'envoi de video",'vod_infomaniak'); ?></h2>
		<p>
			<label style="font-weight: bold"><?php _e("Dossier d'envoi",'vod_infomaniak'); ?> :</label>
			<span><img src="<?php echo plugins_url('vod-infomaniak/img/ico-folder-open-16x16.png'); ?>" style="vertical-align:bottom"/> <?php echo $oFolder->sName; ?> ( '<?php echo $oFolder->sPath; ?>' )</span>
		</p>
		<p>
			<label style="font-weight: bold"><?php _e("Limites",'vod_infomaniak'); ?> :</label>
			<ul style="list-style: disc inside; margin-left: 20px;">
				<li><?php _e("Le poids des fichiers envoyes via ce module est limite a 1Go",'vod_infomaniak'); ?></li>
				<li><?php _e("Les formats videos supportes sont avi, flv, mov, mpeg, mp4, mkv, rm, wmv, m4v, vob, 3gp, webm, f4v, ts",'vod_infomaniak'); ?></li>
				<li><?php _e("L'envoi doit etre effectue en moins de 4 heures",'vod_infomaniak'); ?></li>
			</ul>
		</p>
		<p><label style="font-weight: bold"><?php _e("Envoi",'vod_infomaniak'); ?> :</label></p>
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
		<h2><?php _e("Utilitaire d'importation de video",'vod_infomaniak'); ?></h2>
		
		<form name="adminForm" action="<?php echo $action_url; ?>" method="post">
			<input type="hidden" name="submit" value="1"/>
			<input type="hidden" name="sAction" value="popupImport"/>
			<input type="hidden" name="iFolder" value="<?php echo $oFolder->iFolder; ?>"/>
			<p>
				<label style="font-weight: bold"><?php _e("Dossier d'envoi",'vod_infomaniak'); ?> :</label>
				<span><img src="<?php echo plugins_url('vod-infomaniak/img/ico-folder-open-16x16.png'); ?>" style="vertical-align:bottom"/> <?php echo $oFolder->sName; ?> ( '<?php echo $oFolder->sPath; ?>' )</span>
			</p>
			<p>
				<label style="font-weight: bold"><?php _e("Limites",'vod_infomaniak'); ?> :</label>
				<ul style="list-style: disc inside; margin-left: 20px;">
					<li><?php _e("Le poids des fichiers envoyes via ce module est limite a 1Go",'vod_infomaniak'); ?></li>
					<li><?php _e("Les formats videos supportes sont avi, flv, mov, mpeg, mp4, mkv, rm, wmv, m4v, vob, 3gp, webm, f4v, ts",'vod_infomaniak'); ?></li>
				</ul>
			</p>
			<p>
				<label style="font-weight: bold"><?php _e("Adresse",'vod_infomaniak'); ?> :</label>
				<select name="sProtocole" id="sProtocole">
					<option value="http">http://</option>
					<option value="https">https://</option>
					<option value="ftp">ftp://</option>
				</select>
				<input type="text" onkeyup="checkURL();" showsuccess="false" style="width: 50%" value="" name="sUrl" id="sUrl">
			</p>
			<p>
				<input type="checkbox" value="1" onclick="checkAuth();" name="bNeedAuth" id="bNeedAuth">
				<?php _e("Cette adresse necessite une authentification.",'vod_infomaniak'); ?>
			</p>
			<p id="authLine">
				<label style="font-weight: bold"><?php _e("Login",'vod_infomaniak'); ?> :</label> <input type="text" name="sLogin">
				<label style="font-weight: bold"><?php _e("Password",'vod_infomaniak'); ?> :</label> <input type="password" name="sPassword">
			</p>
			<div class="submit"><input type="submit" name="Submit" value="<?php _e("Importer",'vod_infomaniak'); ?>" /></div>
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

	static function managementMenu( $action_url, $sPagination, $aOptions, $aVideos){
		?>
		<h2><?php _e("Gestionnaire de videos",'vod_infomaniak'); ?></h2>

		<div class="tablenav" style="padding-right: 20px;">
			<div class='tablenav-pages'>
				<?php echo $sPagination; ?>
			</div>
		</div>

		<div id="dialog-confirm-vod" title="<?php _e("Supprimer une video",'vod_infomaniak'); ?>" style="display:none;">
			<form id="adminFormVodDelete"name="adminForm" action="<?php echo $action_url; ?>" method="POST">
				<input type="hidden" name="submitted" value="1" />
				<input type="hidden" name="sAction" value="delete" />
				<input type="hidden" id="dialog-confirm-id" name="dialog-confirm-id" value=""/>
				<p style="padding-left: 10px;">
					<?php _e("Vous etes sur le point de supprimer la video",'vod_infomaniak'); ?> '<span id="dialog-confirm-title" style="font-weight: bold;"></span>'.<br/><br/>
					<span style="color: darkRed; font-style:italic;">
						<span  style="font-weight: bold;"><?php _e("Attention",'vod_infomaniak'); ?> :</span>
						<?php _e("C'est une suppression definitive de la video, il n'y pas de corbeille ou de moyen de la recuperer une fois effacer.",'vod_infomaniak'); ?>
					</span><br/><br/>
					<?php _e("Etes-vous sur de vouloir continuer ?",'vod_infomaniak'); ?>
				</p>
			</form>
		</div>

		<div id="dialog-modal-vod" title="<?php _e("Previsualisation d'une video",'vod_infomaniak'); ?>" style="display:none; padding: 5px; overflow: hidden;">
			<h3 id="dialog-modal-title" style="text-align:center; margin: 5px">Titre</h3>
			<center>
				<iframe id="dialog-modal-video" frameborder="0" width="480" height="320" src="#"></iframe>
			</center>
			<div style="padding-left:5px">
				<h3><?php _e("Informations",'vod_infomaniak'); ?></h3>
				<p>
					<form name="adminForm" action="<?php echo $action_url; ?>" method="POST">
						<input type="hidden" name="submitted" value="1" />
						<input type="hidden" name="sAction" value="rename" />
						<input type="hidden" id="dialog-modal-id" name="dialog-modal-id" value=""/>
						<input type="submit" value="Modifier" style="float:right; margin-right:25px;"/>
						<input id="dialog-modal-name" name="dialog-modal-name" text="" style="float:right; width: 350px; border: 1px solid #CCCCCC; color: #444444; border-radius: 3px; padding: 4px"/>
					</form>	
					<label><?php _e("Nom",'vod_infomaniak'); ?> :</label>	
				</p>
				<p id="dialog-modal-access-block" style="padding-top: 2px;">
					<label><?php _e("Restriction d'acces",'vod_infomaniak'); ?> :</label>
					<span id="dialog-modal-access" style="font-weight: bold; padding-left: 45px;"></span>
				</p>			
				<h3><?php _e("Integration",'vod_infomaniak'); ?></h3>
				<p>
					<a id="dialog-modal-url-href" href="#" target="_blank">
						<img src="<?php echo plugins_url('vod-infomaniak/img/ico-redo.png'); ?>" style="float:right; margin-right:25px; vertical-align:bottom;"  alt="<?php _e("Visualiser la video",'vod_infomaniak'); ?>"/>
					</a>
					<input id="dialog-modal-url" text="" style="float:right; width: 393px; margin-right: 5px; border 1px solid #CCC; border-radius: 3px; background-color: #FFF; margin-top:0; padding: 4px; border: 1px solid #CCCCCC; color: #444444;" readonly="value" onfocus="this.select();"/>
					<label><?php _e("Url de la video",'vod_infomaniak'); ?> :</label>
				</p>
				<p>
					<a id="dialog-modal-url-img-href" href="#" target="_blank">
						<img src="<?php echo plugins_url('vod-infomaniak/img/ico-redo.png'); ?>" style="float:right; margin-right:25px; vertical-align:bottom;" alt="Visualiser l'image"/>
					</a>
					<input id="dialog-modal-url-img" text="" style="float:right; width: 393px; margin-right: 5px; border 1px solid #CCC; border-radius: 3px; background-color: #FFF; margin-top:0; padding: 4px; border: 1px solid #CCCCCC; color: #444444;" readonly="value" onfocus="this.select();"/>
					<label><?php _e("Url de l'image",'vod_infomaniak'); ?> :</label>
				</p>
				<p>
					<input id="dialog-modal-balise" text="" style="float:right; margin-right:25px; width: 414px; border 1px solid #CCC; border-radius: 3px; background-color: #FFF; margin-top:0; padding: 4px; border: 1px solid #CCCCCC; color: #444444;" readonly="value" onfocus="this.select();"/>
					<label><?php _e("Code d'integration",'vod_infomaniak'); ?> :</label>
				</p>
			</div>
			<div style="padding-top: 15px; text-align:center;">
				<ul style="display:inline; ">
					<li style="display:inline">
						<a id="dialog-modal-admin" href="#" target="_blank" style="text-decoration: none; color:#444444; font-weight: bold;">
							<img src="<?php echo plugins_url('vod-infomaniak/img/ico-video.png'); ?>" alt="<?php _e("Administrer cette video",'vod_infomaniak'); ?>" style="vertical-align:bottom"/> <?php _e("Administrer cette video",'vod_infomaniak'); ?>
						</a>
					</li>
					<li style="display:inline; padding-left: 20px">
						<a id="dialog-modal-admin2" href="#" target="_blank" style="text-decoration: none; color:#444444; font-weight: bold;">
							<img src="<?php echo plugins_url('vod-infomaniak/img/ico-statistics.png'); ?>" alt="<?php _e("Voir les statistiques de cette video",'vod_infomaniak'); ?>" style="vertical-align:bottom"/> <?php _e("Voir les statistiques",'vod_infomaniak'); ?>
						</a>
					</li>
					<li style="display:inline; padding-left: 20px">
						<form id="adminFormPost" name="adminFormPost" action="<?php echo $action_url; ?>" method="POST" style="display:none">
							<input type="hidden" name="submitted" value="1" />
							<input type="hidden" name="sAction" value="post" />
							<input type="hidden" id="dialog-post-id" name="dialog-post-id" value=""/>
						</form>
						<a id="dialog-modal-admin3" href="javascript:;" onclick="jQuery('#adminFormPost').submit();" style="text-decoration: none; color:#444444; font-weight: bold;">
							<img src="<?php echo plugins_url('vod-infomaniak/img/ico-edit.png'); ?>" alt="<?php _e("Creer un article",'vod_infomaniak'); ?>" style="vertical-align:bottom"/> <?php _e("Creer un article",'vod_infomaniak'); ?>
						</a>
					</li>
				</ul>
			</div>
		</div>

		<table class="widefat" style="width: 99%">
			<thead>
				<tr>
					<th width="50%"><?php _e("Video",'vod_infomaniak'); ?></th>
					<th><?php _e("Dossier",'vod_infomaniak'); ?></th>
					<th><?php _e("Date d'upload",'vod_infomaniak'); ?></th>
					<th width="80"><?php _e("Action",'vod_infomaniak'); ?></th>			
				</tr>
			</thead>
			<tbody>
				<?php
					foreach( $aVideos as $oVideo ){
				?>
				<tr>
					<td>
						<img src="<?php echo plugins_url('vod-infomaniak/img/videofile.png'); ?>" style="vertical-align:bottom"/>
						<a href="javascript:; return false;" onclick="openVodPopup('<?php echo $oVideo->iVideo; ?>', '<?php echo $oVideo->sName; ?>','<?php echo $oVideo->sPath.$oVideo->sServerCode; ?>', '<?php echo strtolower($oVideo->sExtension);?>', '<?php echo strtolower($oVideo->sAccess);?>', '<?php echo $oVideo->sToken;?>', '<?php echo $oVideo->iFolder;?>'); return false;"><?php echo ucfirst(stripslashes($oVideo->sName)); ?></a>
					</td>
					<td><img src="<?php echo plugins_url('vod-infomaniak/img/ico-folder-open-16x16.png'); ?>" style="vertical-align:bottom"/> <?php echo $oVideo->sPath; ?></td>
					<td><?php echo $oVideo->dUpload; ?></td>
					<td>
						<a href="javascript:; return false;" onclick="openVodPopup('<?php echo $oVideo->iVideo; ?>', '<?php echo $oVideo->sName; ?>','<?php echo $oVideo->sPath.$oVideo->sServerCode."', '".strtolower($oVideo->sExtension);?>', '<?php echo strtolower($oVideo->sAccess);?>', '<?php echo $oVideo->sToken;?>', '<?php echo $oVideo->iFolder;?>'); return false;"><img src="<?php echo plugins_url('vod-infomaniak/img/ico-information.png'); ?>" alt="<?php _e("Information sur cette video",'vod_infomaniak'); ?>"/></a>
						<a href="https://statslive.infomaniak.com/vod/videoDetail.php?iVodCode=<?php echo $aOptions['vod_api_icodeservice'];?>&iFileCode=<?php echo $oVideo->iVideo; ?>" target="_blank"><img src="<?php echo plugins_url('vod-infomaniak/img/ico-video.png'); ?>" alt="<?php _e("Administrer cette video",'vod_infomaniak'); ?>"/></a>
						<a href="https://statslive.infomaniak.com/vod/videoDetail.php?iVodCode=<?php echo $aOptions['vod_api_icodeservice'];?>&iFileCode=<?php echo $oVideo->iVideo; ?>&tab=2" target="_blank"><img src="<?php echo plugins_url('vod-infomaniak/img/ico-statistics.png'); ?>" alt="<?php _e("Voir les statistiques de cette video",'vod_infomaniak'); ?>"/></a>
						<a href="javascript:; return false;" onclick="confirmVodDelete('<?php echo $oVideo->iVideo; ?>', '<?php echo $oVideo->sName; ?>');"><img src="<?php echo plugins_url('vod-infomaniak/img/ico-delete.png'); ?>" alt="<?php _e("Supprimer cette video",'vod_infomaniak'); ?>"/></a>
					</td>
				</tr>
				<?php	} ?>
			</tbody>
			<script>
				confirmVodDelete = function( iVideo, sTitle ){
					jQuery("#dialog-confirm-id").val( iVideo );
					jQuery("#dialog-confirm-title").text( sTitle );
					jQuery("#dialog-confirm-vod").dialog({
						resizable: false,
						width: 600,
						height:210,
						modal: true,
						buttons: {
							"<?php _e("Supprimer definitivement la video",'vod_infomaniak'); ?>": function() {
								jQuery('#adminFormVodDelete').submit();
							},
							"<?php _e("Annuler",'vod_infomaniak'); ?>": function() {
								jQuery( this ).dialog( "close" );
							}
						}
					});
				}
				openVodPopup = function( iVideo, title, url, sExtension, sAccess, sToken, iFolder ){
					var urlComplete = "<?php echo $aOptions['vod_api_id'];?>"+url;
					var sParam = "";
					if( sToken != "" ){
						sParam = "?sKey="+sToken;
						sBalise = "vod tokenfolder='"+iFolder+"'";
					}else{
						sBalise = 'vod';
					}
					jQuery("#dialog-modal-id").val( iVideo );
					jQuery("#dialog-post-id").val( iVideo );
					jQuery("#dialog-modal-title").text( title );
					jQuery("#dialog-modal-name").val( title );
					jQuery("#dialog-modal-url").val( "http://vod.infomaniak.com/redirect/"+urlComplete+"."+sExtension );
					jQuery("#dialog-modal-url-href").attr( "href", "http://vod.infomaniak.com/redirect/"+urlComplete+"."+sExtension+sParam );
					jQuery("#dialog-modal-url-img").val( "http://vod.infomaniak.com/redirect/"+urlComplete+".jpg" );
					jQuery("#dialog-modal-url-img-href").attr( "href", "http://vod.infomaniak.com/redirect/"+urlComplete+".jpg" );
					jQuery("#dialog-modal-balise").val( "["+sBalise+"]"+url+"."+sExtension+"[/vod]" );
					jQuery("#dialog-modal-admin").attr( "href", "https://statslive.infomaniak.com/vod/videoDetail.php?iVodCode=<?php echo $aOptions['vod_api_icodeservice'];?>&iFileCode="+iVideo );
					jQuery("#dialog-modal-admin2").attr( "href", "https://statslive.infomaniak.com/vod/videoDetail.php?iVodCode=<?php echo $aOptions['vod_api_icodeservice'];?>&iFileCode="+iVideo+"&tab=2" );
					jQuery("#dialog-modal-video").attr( "src", "http://vod.infomaniak.com/iframe.php?url="+urlComplete+"."+sExtension+sParam+"&player=576&vod=214&preloadImage="+urlComplete+".jpg" );

					textAccess = "";
					if( sAccess != '' && sAccess != 'all' ){
						textAccess += "<?php _e("Video Geolocalise",'vod_infomaniak'); ?>";
					}
					if( sToken != "" ){
						if( textAccess != "" ) textAccess += ", ";
						textAccess += "<?php _e("Securise avec un token",'vod_infomaniak'); ?>";
					}
					if( textAccess != "" ){
						jQuery("#dialog-modal-access").text( textAccess );
						jQuery("#dialog-modal-access-block").show();
					}else{
						jQuery("#dialog-modal-access-block").hide();
					}
					
					jQuery("#dialog-modal-vod").dialog({
						width: 620,
						height: 655,
						resizable: false,
						beforeClose: function(event, ui) {
							jQuery( "#dialog-modal-video").attr( "src", "#");
						}
					});
					return false;
				}
			</script>
		</table>

		<div class="tablenav" style="padding-right: 20px;">
			<div class='tablenav-pages'>
				<?php echo $sPagination; ?>
			</div>
		</div>
		<?php
	}

	static function playlistMenu($actionurl, $options, $aPlaylist){
		?>
		<h2><?php _e("Playlists",'vod_infomaniak'); ?></h2>
		<p><?php printf( __("Si vous desirez ajouter ou modifier les playlist ci-dessous, veuillez vous rendre dans <a href='https://statslive.infomaniak.com/vod/playlists.php?iVodCode=%s' target='_blank'>la console d'administration</a>",'vod_infomaniak'), $options['vod_api_icodeservice']); ?></p>

		<h2><?php _e("Precedents Envois",'vod_infomaniak'); ?></h2>
			<table class='widefat' style='width: 99%'>
				<thead>
					<tr>
						<th width="20%"><?php _e("Nom",'vod_infomaniak'); ?></th>
						<th width="30%"><?php _e("Description",'vod_infomaniak'); ?></th>
						<th><?php _e("Nombre videos",'vod_infomaniak'); ?></th>
						<th><?php _e("Mode de lecture",'vod_infomaniak'); ?></th>
						<th><?php _e("Date",'vod_infomaniak'); ?></th>
						<th width="80px"><?php _e("Action",'vod_infomaniak'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach( $aPlaylist as $oPlaylist ){ ?>
					<tr>
						<td><img src="<?php echo plugins_url('vod-infomaniak/img/ico-display-list.png'); ?>" style="vertical-align:bottom; padding: 0px 5px;"/> <?php echo ucfirst($oPlaylist->sPlaylistName); ?></td>
						<td><?php echo ucfirst($oPlaylist->sPlaylistDescription); ?></td>
						<td><?php echo $oPlaylist->iTotal; ?></td>
						<td><?php echo $oPlaylist->sMode; ?></td>
						<td><?php echo $oPlaylist->dCreated; ?></td>
						<td>
							<a href="https://statslive.infomaniak.com/vod/playlists.php?iVodCode=<?php echo $options['vod_api_icodeservice'];?>&sAction=showPlaylist&iPlaylistCode=<?php echo $oPlaylist->iPlaylistCode; ?>" target="_blank"><img src="<?php echo plugins_url('vod-infomaniak/img/ico-information.png'); ?>" alt="<?php _e("Administrer cette playlist",'vod_infomaniak'); ?>"/></a>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php
	}

	static function implementationMenu( $actionurl, $options, $aPlayers ){
		?>
		<h2><?php _e("Integration par defaut des videos",'vod_infomaniak'); ?></h2>
		<form name="adminForm" action="<?php echo $action_url; ?>" method="post">
			<input type="hidden" name="submitted" value="1" /> 
			<table>
				<tr>
					<td style="vertical-align: top">
						<label><?php _e("Selection du player par defaut",'vod_infomaniak'); ?> :</label><br/>
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
						</select> <input type="submit" name="Submit" value="<?php _e("Choisir ce player",'vod_infomaniak'); ?>" />
					
						<p><?php _e("Informations sur ce Player",'vod_infomaniak'); ?> :</p>
						<?php foreach( $aPlayers as $player ){ ?>
							<div id="player-info-<?php echo $player->iPlayer; ?>" class="player-info" style="padding: 5px 15px; border: 1px solid #EEE; display:none; width: 500px;">
								
								<ul>
									<li><b><?php _e("Nom",'vod_infomaniak'); ?> :</b> <?php echo ucfirst($player->sName); ?></li>
									<li><b><?php _e("Date",'vod_infomaniak'); ?> :</b> <?php echo date("d M Y H:i", strtotime($player->dEdit)); ?></li>
									<li><b><?php _e("Resolution",'vod_infomaniak'); ?> :</b> <?php echo $player->iWidth; ?>x<?php echo $player->iHeight; ?></li>
									<li><b><?php _e("Demarrage automatique",'vod_infomaniak'); ?> :</b> <?php echo $player->bAutoPlay==0? 'Non': 'Oui'; ?></li>
									<li><b><?php _e("Lecture en boucle",'vod_infomaniak'); ?> :</b> <?php echo $player->bLoop==0? 'Non': 'Oui'; ?></li>
									<li><b><?php _e("Switch de qualite",'vod_infomaniak'); ?> :</b> <?php echo $player->bSwitchQuality==0? 'Non': 'Oui'; ?></li>
								</ul>
								<div style="text-align:center; width: 100%">
									<a id="dialog-modal-admin" href="https://statslive.infomaniak.com/vod/players/playerConfig.php?iVodCode=<?php echo $options['vod_api_icodeservice'];?>&iPlayerCode=<?php echo $player->iPlayer; ?>" target="_blank" style="text-decoration: none; color:#444444; font-weight: bold;"><img src="<?php echo plugins_url('vod-infomaniak/img/ico-edit.png'); ?>" alt="<?php _e("Modifier ce Player",'vod_infomaniak'); ?>" style="vertical-align:bottom"/> <?php _e("Modifier ce Player",'vod_infomaniak'); ?></a>
								</div>
							</div>
						<?php } ?>
					</td>
					<td style="vertical-align: top; padding-left: 25px;">
						<iframe id="player-demo-video" frameborder="0" width="480" height="360" src="#"></iframe>
					</td>
				</tr>
			</table>
		</form>
		
		<h2><?php _e("Creation ou modification de players",'vod_infomaniak'); ?></h2>
		<p><?php printf( __("Afin de modifier ou creer de nouveaux players flash, nous vous invitons a vous rendre dans votre administration vod : <a href='https://statslive.infomaniak.com/vod/player.php?iVodCode=%d' target='_blank'>Acceder a la configuration des players</a>",'vod_infomaniak'), $options['vod_api_icodeservice']); ?></p>
		<h2><?php _e("Plus d'options",'vod_infomaniak'); ?></h2>
		<p>
			<?php _e("Il est possible de personnaliser votre player video afin qu'ils s'integre parfaitement a votre site. Voici quelques fonctionnalites avancees qu'il propose :",'vod_infomaniak'); ?>
			<ul style="margin-left: 15px; list-style: disc inside;">
				<?php _e("<li>Fonction de seek permettant de se deplacer dans une video meme longue quasi instantanement.</li>
				<li>Bouton permettant de changer a la vole entre les differentes qualites d'une video.</li>
				<li>Bouton d'export sur certains reseau sociaux (facebook, twitter) afin que votre video soit plus visible.</li>
				<li>Player exportable afin de permettre aux visiteurs de recuperer le code d'integration.</li>
				<li>Facilite de personnalisation du player via de rapides modifications (couleurs de la barre, taille de la barre, logo) ou l'utilisation d'une barre entierement personnaliser.</li>
				<li>Compatibilite avec adswizz afin d'ajouter facilement des publicites avant ou apres les videos.</li>
				<li>Et encore d'autres options a decouvrir ...</li>",'vod_infomaniak'); ?>
			</ul>
		</p>
		<script>
			PlayerInfo = function(){
				jQuery('.player-info').hide();
				value = jQuery('#selectPlayer').val();
				console.log(value);
				jQuery('#player-info-'+value).show();
				jQuery("#player-demo-video").attr( "src", "http://vod.infomaniak.com/iframe.php?url=infomaniak_11_vod/demo-2362/mp4-226/big_buck_bunny_720p_h264.mp4&player="+value+"&vod=<?php echo $options['vod_api_icodeservice'];?>&preloadImage=infomaniak_11_vod/demo-2362/mp4-226/big_buck_bunny_720p_h264.jpg" );
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
