<?php
/**
 * Classe permettant d'utiliser simplement les differentes fonctions de l'API vod.
 * Il est parfaitement possible d'utiliser cette classe independamment du plugin wordpress.
 * En cas de problemes ou de questions, veuillez contacter streaming@infomaniak.ch
 *
 * @author Destrem Kevin
 * @link http://statslive.infomaniak.ch/vod/api/
 * @version 1.0
 * @copyright infomaniak.ch
 *
 */

class vod_api {
	
	protected $sLogin = "";
	protected $sPassword = "";
	protected $sId = "";
	private $oSoap;

	/**
	 * Constructeur prennant les informations de connexions
	 * 
	 * @param string $sLogin Login de connexion
	 * @param string $sPassword Mot de passe associe au login
	 * @param string $sId Identifiant de l'espace VOD
	 */
	public function __construct($sLogin, $sPassword, $Id=""){
		$this->sLogin = $sLogin;
		$this->sPassword = $sPassword;
		$this->sId = $Id;
	}

	/**
	 * Fonction permettant de tester la connectivite avec l'API
	 *
	 * @return boolean
	 */
	public function ping(){
		$oSoap = $this->getSoapAdmin();
		if( !empty( $oSoap ) ){
			return $oSoap->ping();
		}
		return false;
	}
	
	/**
	 * Fonction permettant de tester la connectivite avec l'API
	 *
	 * @return integer
	 */
	public function time(){
		$oSoap = $this->getSoapAdmin();
		if( !empty( $oSoap ) ){
			return $oSoap->time();
		}
		return false;
	}
	
	/**
	 * Fonction permettant de recuperer l'id de l'espace VOD
	 * 
	 * @return integer
	 */
	public function getServiceItemID(){
		$oSoap = $this->getSoapAdmin();
		if( !empty( $oSoap ) ){
			return intval( $oSoap->getServiceItemID() );
		}
		return 0;
	}

	/**
	 * Fonction permettant de recuperer l'identifiant du groupe auquel est rattache le service
	 * 
	 * @return integer
	 */
	public function getGroupID(){
		$oSoap = $this->getSoapAdmin();
		if( !empty( $oSoap ) ){
			return intval( $oSoap->getGroupeID() );
		}
		return 0;
	}

	/**
	 * Fonction permettant de recuperer le nombre de video
	 * 
	 * @return integer
	 */
	public function countVideo(){
		$oSoap = $this->getSoapAdmin();
		if( !empty( $oSoap ) ){
			return intval($oSoap->countVideo());
		}
		return false;
	}
	
	/**
	 * Fonction permettant de supprimer une video
	 * 
	 * @param integer $iFolderCode
	 * @param string $sFileServerCode
	 * @return boolean
	 */
	public function deleteVideo( $iFolderCode, $sFileServerCode){
		$oSoap = $this->getSoapAdmin();
		if( !empty( $oSoap ) ){
			return $oSoap->deleteVideo( $iFolderCode, $sFileServerCode );
		}
		return false;
	}
	
	/**
	 * Fonction permettant de renommer une video
	 * 
	 * @param integer $iFolderCode
	 * @param string $sFileServerCode
	 * @param string $sName
	 * @return boolean
	 */
	public function renameVideo( $iFolderCode, $sFileServerCode, $sName){
		$oSoap = $this->getSoapAdmin();
		if( !empty( $oSoap ) ){
			return $oSoap->renameVideo($iFolderCode, $sFileServerCode, $sName);
		}
		return false;
	}
	
	/**
	 * Fonction permettant de recuperer les dernieres videos
	 * 
	 * @return array
	 */
	public function getLastVideo( $iLimit, $iPage ){
		$oSoap = $this->getSoapAdmin();
		if( !empty( $oSoap ) ){
			return $oSoap->getLastVideo($iLimit, $iPage);
		}
		return false;
	}
	
	/**
	 * Fonction permettant de recuperer les dernieres importations de videos
	 * 
	 * @return array
	 */
	public function getLastImportation(){
		$oSoap = $this->getSoapAdmin();
		try{
			if( !empty( $oSoap ) ){
				return $oSoap->getLastImportation( 15 );
			}
		}catch (Exception $oException) {}
		return false;
	}
	
	/**
	 * Fonction permettant de recuperer les dossiers de cet espace VOD
	 * 
	 * @return array
	 */
	public function getFolders(){
		$oSoap = $this->getSoapAdmin();
		if( !empty( $oSoap ) ){
			return $oSoap->getFolders();
		}
		return false;
	}
	
	/**
	 * Fonction permettant de savoir s'il y a eu des modifications recemment sur les dossiers
	 * 
	 * @return boolean
	 */
	public function folderModifiedSince( $date ){
		$oSoap = $this->getSoapAdmin();
		if( !empty( $oSoap ) ){
			return $oSoap->folderModifiedSince( $date );
		}
		return false;
	}
	
	/**
	 * Fonction permettant de recuperer les players de cet espace VOD
	 * 
	 * @return array
	 */
	public function getPlayers(){
		$oSoap = $this->getSoapAdmin();
		if( !empty( $oSoap ) ){
			return $oSoap->getPlayers();
		}
		return false;
	}

	/**
	 * Fonction permettant de savoir s'il y a eu des modifications recemment sur les players
	 * 
	 * @return boolean
	 */
	public function playerModifiedSince( $date ){
		$oSoap = $this->getSoapAdmin();
		if( !empty( $oSoap ) ){
			return $oSoap->playerModifiedSince( $date );
		}
		return false;
	}
	
	/**
	 * Fonction permettant de recuperer les playlists de cet espace VOD
	 * 
	 * @return array
	 */
	public function getPlaylists(){
		$oSoap = $this->getSoapAdmin();
		if( !empty( $oSoap ) ){
			return $oSoap->getPlaylists();
		}
		return false;
	}

	/**
	 * Fonction permettant de savoir s'il y a eu des modifications recemment sur les playlist
	 * 
	 * @return boolean
	 */
	public function playlistModifiedSince( $date ){
		$oSoap = $this->getSoapAdmin();
		if( !empty( $oSoap ) ){
			return $oSoap->playlistModifiedSince( $date );
		}
		return false;
	}

	/**
	 * Fonction permettant d'obtenir un token d'upload
	 * 
	 * @return string
	 */
	public function initUpload( $sPath ){
		$oSoap = $this->getSoapAdmin();
		if( !empty( $oSoap ) ){
			return $oSoap->initUpload( $sPath );
		}
		return false;
	}

	/**
	 * Fonction permettant de lancer le telechargement d'une video
	 * 
	 * @return boolean
	 */
	public function importFromUrl( $sPath, $sUrl, $aOptions ){
		$oSoap = $this->getSoapAdmin();
		if( !empty( $oSoap ) ){
			return $oSoap->importFromUrl( $sPath, $sUrl, $aOptions );
		}
		return false;
	}

	/**
	 * Fonction permettant de recuperer l'adresse de callback actuellement en place
	 * 
	 * @return string
	 */
	public function getCallback(){
		$oSoap = $this->getSoapAdmin();
		if( !empty( $oSoap ) ){
			return $oSoap->getCallbackUrl();
		}
		return false;
	}

	/**
	 * Fonction permettant de definir l'adresse de callback
	 * 
	 * @param string $sUrl Nouvelle adresse de callback
	 * @return boolean
	 */
	public function setCallback( $sUrl ){
		$oSoap = $this->getSoapAdmin();
		if( !empty( $oSoap ) ){
			return $oSoap->setCallbackUrl( $sUrl );
		}
		return false;
	}

	
	private function getSoapAdmin(){
		if( !empty($this->oSoap) ){
			return $this->oSoap;
		}else{
			$this->oSoap=new SoapClient('http://statslive.infomaniak.com/vod/api/vod_soap.wsdl', array(
				'trace' 	=> 1,
				'encoding' 	=> 'UTF-8'
			));
			
			try{
				$this->oSoap->__setSoapHeaders(array(new SoapHeader('urn:vod_soap', 'AuthenticationHeader', new SoapVODAuthentificationHeader($this->sLogin, $this->sPassword, $this->sId))));
				return $this->oSoap;
			}catch (Exception $oException) {
				var_dump($oException);
			}
			return false;
		}
	}
}

ini_set("soap.wsdl_cache_enabled", 0);

class SoapVODAuthentificationHeader {
    public $Password;
    public $sLogin;
	public $sVod;
	
    public function __construct($sLogin, $sPassword, $sVod) {
        $this->sPassword=$sPassword;
        $this->sLogin=$sLogin;
        $this->sVod=$sVod;
    }
}
?>
