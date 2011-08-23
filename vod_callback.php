<?php
/**
 * Fichier de callback utilisé comme interface du daemon VOD.
 * Cela permet d'avoir immediatement accès aux vidéos qui viennent d'etre envoyés sur l'espace VOD.
 * En cas de problemes ou de questions, veuillez contacter streaming@infomaniak.ch
 *
 * @author Destrem Kevin
 * @link http://statslive.infomaniak.ch/vod/api/
 * @version 1.0
 * @copyright infomaniak.ch
 *
 */

$response = $_POST;
$aOptions = get_option('vod_infomaniak');

if( $aOptions['vod_api_callbackKey'] == $_REQUEST['key'] ){
	$db = new EasyVod_db();

	$iVideo = intval($response['iFileCode']);
	$iFolder = intval($response['iFolderCode']);
	$sFileName = $response['sFileName'];
	$sServerCode = $response['sFileServerCode'];

	if( empty($iVideo) || empty($iFolder) ){
		die("Problème avec les parametres");
	}
	$oFolder = $db->getFolder( $iFolder );
	if( empty( $oFolder ) || empty( $oFolder->sName) ){
		die("dossier inconnu");
	}
	
	$encodage = array_shift($response['files']);
	$path_tmp = explode('/redirect/'.$aOptions['vod_api_id']."/", $encodage['sFileUrl'] );
	$sPath = "/".dirname ($path_tmp[1])."/";
	$sExtension = strtoupper($encodage['sExtension']);
	$iDuration = intval($response['iDuration']);
	$dUpload = date("Y-m-d H:i:s", strtotime($response['dDateUpload']));

	$oldVideo = $db->get_videos_byCodes( $sServerCode, $iFolder );
	if( !empty($oldVideo) ){
		foreach($oldVideo as $video){
			$db->delete_video( $video->iVideo );
		}
	}
	
	$db->insert_video($iVideo, $iFolder, $sFileName, $sServerCode, $sPath, $sExtension, $iDuration, $dUpload );
}
die();
?>

