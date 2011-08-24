<?php
/*********************************************************************************************
*
*	Description: class handling interceptionfilters
*
*	Author: Niklas Odén
*
***********************************************************************************************/
	
	require_once(TP_SQLPATH . 'config.php');
	
	
class CInterceptionFilter {

	//-----------------------------------------------------------------------------------------
	//
	//	member variables
	//
	

	
	//-----------------------------------------------------------------------------------------
	//
	//	constructor and destructor
	//
	
	function __construct() {
		
		if(SESSION_TIME != 0) {
			CInterceptionFilter::SessionExpireTime();
		}
	}
	
	function __destruct() {
	
	}
	
	//-----------------------------------------------------------------------------------------
	//
	//	session expire handler
	//
	
	public static function SessionExpireTime() {
		// automatic log out enabled when SESSION_TIME != 0
		if(SESSION_TIME != 0) {
			if(isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIME)) {
				require_once(TP_SOURCEPATH . "FSessionDestroy.php");
				session_unset();
					
				header('Location: ' . WS_SITELINK . "?m=core&p=login&expire=expire");
				exit;
			}
			$_SESSION['last_activity'] = time();
		}
	}
	
	//-----------------------------------------------------------------------------------------
	//
	//	checking if frontcontroller is visited or not
	//
	
	public function FrontControllerIsVisited() {
		
		global $frontDoor;
		
		if(!isset($frontDoor)) {
			die('No directaccess to pagecontroller is allowed');
		}
	
	}
	
	
	//-----------------------------------------------------------------------------------------
	//
	//	checking if user is logged in
	//
	
	public function UserLoginStatus($aRedirect = "") {
		
		$redirect = (!empty($aRedirect) ? $aRedirect : "home");
		
		if(!isset($_SESSION['accountUser'])) {
			header('Location: ' . WS_SITELINK . "?p=login&amp;redirect=" . $redirect);
			exit;
		}
	
	}
	
	
	//-----------------------------------------------------------------------------------------
	//
	//	checking if user has admin rights
	//
	
	public function UserGroupStatus() {
		
		if($_SESSION['groupMemberUser'] != 'adm') {
			die('You have no permisson to visit this page');
		}
		
	}
	
	//-----------------------------------------------------------------------------------------
	//
	//	checking if user has admin rights or is a specific user
	//
	
	public function UserIsAdminOrCurrent($aUserId) {
	
		$isAdmGroup         = (isset($_SESSION['groupMemberUser']) && $_SESSION['groupMemberUser'] == 'adm') ? TRUE : FALSE;
        $isCurrentUser    = (isset($_SESSION['idUser']) && $_SESSION['idUser'] == $aUserId) ? TRUE : FALSE;
		
		return $isAdmGroup || $isCurrentUser;
	}
	
	//-----------------------------------------------------------------------------------------
	//
	//	checking if user has admin rights
	//
	
	public function UserIsAdmin() {
	
		$isAdmGroup         = (isset($_SESSION['groupMemberUser']) && $_SESSION['groupMemberUser'] == 'adm') ? TRUE : FALSE;
		
		return $isAdmGroup;
	}
	
	
	} // end of class
?>