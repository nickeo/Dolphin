<?php
/**************************************************************************************************
*
*	description: 	class handling different tasks needed by the pages
*
*	author:			Niklas Odén
*
**************************************************************************************************/

	require_once(TP_SQLPATH . 'CSQL.php');
	require_once(TP_SQLPATH . 'config.php');

class CPageController {


	//---------------------------------------------------------------------------------------------
	//
	//	member variables
	//
	public $lang = Array();
	
	//---------------------------------------------------------------------------------------------
	//
	//	constructor and destructor
	//
	
	function __construct() {
		
        $_SESSION['redirect'] = CPageController::SetSESSIONRedirect();
        
        // $_SESSION['history2'] = CPageController::SESSIONIsSetOrSetDefault('history1');
        // $_SESSION['history1'] = CPageController::CurrentURL();
	}
	
	function __destruct() {
	
	}
	
	//----------------------------------------------------------------------------------------------
	//
	//	
	//
	
	public static function SetSESSIONRedirect() {
		
		$appDir = APP_DIRECTORY; // root directory of the application
		$getUri = $_SERVER['REQUEST_URI'];
		$pos = stripos($getUri, $appDir);
		
		return ($pos !== false) ? substr($getUri, $pos + strlen($appDir)) : "?p=home";
		
	}
	
	//----------------------------------------------------------------------------------------------
	//
	//	check if chosen SESSION-variable is set, if isset return SESSION else return default
	//
	
	public static function SESSIONIsSetOrSetDefault($aEntry, $aDefault='') {
	
		return isset($_SESSION["$aEntry"]) && !empty($_SESSION["$aEntry"]) ? $_SESSION["$aEntry"] : $aDefault;
	}
	
	//----------------------------------------------------------------------------------------------
	//
	//	check if chosen POST-variable is set, if isset return POST else return default
	//
	
	public static function POSTIsSetOrSetDefault($aEntry, $aDefault='') {
	
		return isset($_POST["$aEntry"]) && !empty($_POST["$aEntry"]) ? $_POST["$aEntry"] : $aDefault;
	}
	
	//----------------------------------------------------------------------------------------------
	//
	//	check if chosen GET-variable is set, if isset return GET else return default
	//
	
	public static function GETIsSetOrSetDefault($aEntry, $aDefault='') {
	
		return isset($_GET["$aEntry"]) && !empty($_GET["$aEntry"]) ? $_GET["$aEntry"] : $aDefault;
	}
	
	// ---------------------------------------------------------------------------------------------
    //
    // Create a URL to the current page.
    //
    public static function CurrentURL() {

        // Create link to current page
        $refToThisPage = "http";
        $refToThisPage .= (@$_SERVER["HTTPS"] == "on") ? 's' : '';
        $refToThisPage .= "://";
        $refToThisPage .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        
        return $refToThisPage;
    }
	
	
	// ------------------------------------------------------------------------------------
	//
	// Static function
	// Redirect to another page
	// Support $aUri to be local uri within site or external site (starting with http://)
	// If empty, redirect to home page of current module.
	//
	public static function RedirectTo($aUri) {

		if(empty($aUri)) {
		CPageController::RedirectToModuleAndPage();
		} else if(!strncmp($aUri, "http://", 7)) {
			;
		} else if(!strncmp($aUri, "?", 1)) {
			$aUri = WS_SITELINK . "{$aUri}";
		} else {
			$aUri = WS_SITELINK . "?p={$aUri}";
		}

		header("Location: {$aUri}");
		exit;
	}


	// ------------------------------------------------------------------------------------
	//
	// Static function
	// Redirect to another local page using module, page and arguments (Array)
	// Defaults to current module home-page.
	//
	public static function RedirectToModuleAndPage($aModule='', $aPage='home', $aArguments='') {
	
		global $gModule;

		$m = (empty($aModule)) ? "m={$gModule}" : "m={$aModule}";
		$p = "p={$aPage}";
		$aUri = WS_SITELINK . "?{$m}&{$p}";

		// Enable sending $aArguments as an Array later on. When needed.

		header("Location: {$aUri}");
		exit;
	}



	
	// ---------------------------------------------------------------------------------------------
    //
    // check password
    //
	
	public function CheckPassword($aPassword, $aConfirmation, $aFailRedirect) {
		unset($_SESSION['errorMessage']);
		$failure = false;
		if(empty($aPassword) || empty($aConfirmation)) {
			$_SESSION['errorMessage'] = "Lösenord måste innehålla tecken";
			$failure = true;
		}
		
		if($aPassword != $aConfirmation) {
			$_SESSION['errorMessage'] = "Lösenorden överrensstämmer ej";
			$failure = true;
		}
		
		if(strlen($aPassword) < 7) {
			$_SESSION['errorMessage'] = "Lösenord för kort!";
			$failure = true;
		}
		
		if($failure == true) {
			unset($_SESSION['redirect']);
			header('Location: ' . WS_SITELINK . "{$aFailRedirect}");
			exit;
		}

	}
	
	// ------------------------------------------------------------------------------------
	//
	// Load language file
	//
	public function LoadLanguage($aFilename) {

		
		// All language files in the a lang-subdirectory from the original file.
		$file = basename($aFilename);
		$dir = dirname($aFilename);
		$langFile = $dir . '/lang/' . WS_LANGUAGE . '/' . $file;

		if(!file_exists($langFile)) {
			die(sprintf("Language file does not exists: %s", $langFile));
		}

		require_once($langFile);
		$this->lang = array_merge($this->lang, $lang);
	}
	
	// ------------------------------------------------------------------------------------
	//
	// create gravatar link
	//
	
	public function CreateGravatarLink($aEmail, $aSize) {
	
		if($aEmail == "" || $aEmail == NULL || USER_GRAVATAR == false) {
			$gravatarLink = false;
		} else {
			$gravatarLink = 'http://www.gravatar.com/avatar/' . md5(strtolower($aEmail)) . '.jpg?s=' . $aSize;
		}
		
		return $gravatarLink;
	}
	
}	// end of class
?>