/**
 * Regroupement de fonctions JS permettant d'utiliser le plugin VOD
 *
 * @author Destrem Kevin
 * @link http://statslive.infomaniak.ch/vod/api/
 * @version 1.0
 * @copyright infomaniak.ch
 *
 */
//Fonction permettant d'afficher ou non les options d'integration
Vod_dialogToggleSlider = function(){
	if ( !jQuery("#dialog-slide-header").hasClass("selected") ) {
		jQuery("#dialog-slide-header").addClass("selected");
		jQuery("#dialog-slide").show();
	} else {
		jQuery("#dialog-slide-header").removeClass("selected");
		jQuery("#dialog-slide").hide();
	}
	jQuery("#dialog-vod-form").dialog( "option", {'position' : 'center'} );
};

//Fonction permettant de cacher l'overlay de configuration
Vod_dialogOpen = function () {
	jQuery("#dialog-url-input").value = "";
	jQuery("#dialog-slide-header").removeClass("selected");
	jQuery("#dialog-vod-form").dialog('open');
	jQuery("#dialog-url-input").focus();
}

//Fonction permettant de cacher l'overlay de configuration
Vod_dialogClose = function () {
	jQuery("#dialog-vod-form").dialog("close");
	jQuery("#dialog-slide").hide();
};

Vod_selectVideo = function (sUrl,sToken,iFolder) {
	jQuery('#dialog-url-input').val( sUrl );
	if( sToken != "" ){
		jQuery('#dialog-token').val( iFolder );
	}else{
		jQuery('#dialog-token').val( "" );
	}
	jQuery('#dialog-tabs').tabs( "select" , 1 )
};

sVodUploadParameters = "";

Vod_importVideo = function () {
	if ( jQuery('#uploadSelectFolder').val() > 0 ){
		jQuery('#vodUploadVideo').show();
		jQuery.ajax({
			url: jQuery("#url_ajax_import_video").val(),
			cache: false,
			processData: false,
			data: "iFolder="+jQuery('#uploadSelectFolder').val(),
			success: function(sToken){
				try {
					jQuery('#dialog-tabs').tabs( "disable", [0,1,2,3] );
					sVodUploadParameters = sToken;
					flashUpload( sToken );
				}catch( e ){
					alert('ERROR : '+e);
				}
			}
		});
	} else {
		jQuery('#dialog-tabs').tabs( "enable", [0,1,2,3] );
		jQuery('#vodUploadVideo').hide();
	}
};

//Fonction permettant la validation du formulaire suivant les options choisis
Vod_dialogValid = function () {
	var url = jQuery("#dialog-url-input").val();
	if ( url == null || url == '' ){
		alert('Veuillez saisir une adresse de vidéo valide.');
	}else{
		if( jQuery('#dialog-tabs').tabs('option', 'selected') == 0 || jQuery('#dialog-tabs').tabs('option', 'selected') == 2 || jQuery('#dialog-tabs').tabs('option', 'selected') == 3) {
			alert("Vous devez selectionner une vidéo à ajouter.");
		} else if ( !jQuery("#dialog-slide-header").hasClass('selected') && jQuery('#dialog-token').val()=="" ) {
			var text = "[vod]" + url + "[/vod]";
		} else {
			//Il y a des options d'integration
			var width = jQuery("#dialog-width-input").val();
			var height = jQuery("#dialog-height-input").val();
			var playerDefault = jQuery("#dialog-player-default").val();
			var player = jQuery("#dialog-player").val();
			var tokenFolder = jQuery('#dialog-token').val();
			var text = '[vod';
			if( width != '' ){
				text += " width='"+width+"'";
			}
			if( height != '' ){
				text += " height='"+height+"'";
			}
			if( player != playerDefault ){
				text += " player='"+player+"'";
			}
			if( tokenFolder != '' ){
				text += " tokenfolder='"+tokenFolder+"'";
			}

			if( jQuery("#dialog-slide-header").hasClass('selected') ){
				//Celles qu'on ajoute à chaque fois
				var stretch = jQuery("#dialog-stretch").attr('checked') ? 1 : 0;
				var autostart = jQuery("#dialog-autostart").attr('checked') ? 1 : 0;
				var loop = jQuery("#dialog-loop").attr('checked') ? 1 : 0;	
				text += " stretch='"+ parseInt(stretch)+"'";
				text += " autoplay='"+ parseInt(autostart)+"'";
				text += " loop='"+ parseInt(loop)+"'";
			}

			text += ']' + url + "[/vod]";
		}

		if ( typeof tinyMCE != 'undefined' && ( ed = tinyMCE.activeEditor ) && !ed.isHidden() ) {
			ed.focus();
			if (tinymce.isIE){
				ed.selection.moveToBookmark(tinymce.EditorManager.activeEditor.windowManager.bookmark);
			}
			ed.execCommand('mceInsertContent', false, text);
		} else{
			edInsertContent(edCanvas, text);
		}

		Vod_dialogClose();
	}
};

//Fonction execute a l'initialisation du tinyMCE
(function() {
	tinymce.create('tinymce.plugins.vodplugin', {
		init : function(ed, url){
		jQuery('#dialog-vod-form').dialog({
			title: 'Ajout d\'une video de la VOD',
			resizable: false,
			autoOpen: false,
			width: 750,
			modal: true,
			buttons: {
				"Ajouter": function() {
					var bValid = true;
					if ( bValid ) {
						Vod_dialogValid();
					}
				},
				Cancel: function() {
					Vod_dialogClose();
				}
			}
		});

		jQuery('#dialog-tabs').tabs({
			show: function(event, ui) {
				//On reinit le dossier d'upload lors d'un changement de tab
				if( jQuery('#dialog-tabs').tabs('option', 'selected') == 2 ){
					jQuery('#uploadSelectFolder').val(-1);
					Vod_importVideo();
				}
				//On switch le menu d'implementation et le bouton Ajouter
				if( jQuery('#dialog-tabs').tabs('option', 'selected') == 0 || jQuery('#dialog-tabs').tabs('option', 'selected') == 2 || jQuery('#dialog-tabs').tabs('option', 'selected') == 3){
					jQuery('.ui-dialog-buttonpane button').eq(0).button('disable');
					jQuery('#dialog-config').hide();
					jQuery("#dialog-search-input-video").focus();
				}else{
					jQuery('.ui-dialog-buttonpane button').eq(0).button('enable');
					jQuery('#dialog-config').show();
					jQuery("#dialog-url-input").focus();
				}
				jQuery("#dialog-vod-form").dialog( "option", {'position' : 'center'} );
			}
		});

		jQuery('#dialog-search-input-video').suggest(jQuery('#url_ajax_search_video').val(), {
			delay : 150,
			onSelect : function(){
			part = this.value.split(';;;');
			jQuery('#dialog-search-input-video').val('');
			jQuery('#dialog-url-input').val(part[0]);
			if( part.length == 3 ){
				jQuery('#dialog-token').val( part[1] );
			}else{
				jQuery('#dialog-token').val("");
			}
			jQuery('#dialog-tabs').tabs( "select" , 1 )
		}
		});

		jQuery('#dialog-search-input-playlist').suggest(jQuery('#url_ajax_search_playlist').val(), {
			delay : 150,
			onSelect : function(){
			part = this.value.split(';;;');
			jQuery('#dialog-search-input-playlist').val('');
			jQuery('#dialog-url-input').val(part[0]);
			jQuery('#dialog-tabs').tabs( "select" , 1 )
		}
		});

		checkSearchType = function(){
			if( jQuery('input[type=radio][name=searchtype]:checked').attr('value') == "video" ){
				jQuery('#dialog-search-input-video').show();
				jQuery('#dialog-search-input-playlist').hide();
			}else{
				jQuery('#dialog-search-input-video').hide();
				jQuery('#dialog-search-input-playlist').show();
			}
		};
		checkSearchType();

		ed.addButton('vodplugin', {
			title : 'Inserer VOD',
			image: url + "/../img/videofile.png",
			onclick : function() {
			Vod_dialogOpen();
		}
		});
	}
	});
	tinymce.PluginManager.add('vodplugin', tinymce.plugins.vodplugin);
})();
