<?php
/**********************************************************************************************
*
*	Dolphin , software to build webbapplications.
*	Copyright (C) 2011 Niklas Odén (niklasoden@hotmail.com)
*
* 	This file is part of Dolphin.
*
* 	Dolphin is free software: you can redistribute it and/or modify
* 	it under the terms of the GNU General Public License as published by
* 	the Free Software Foundation, either version 3 of the License, or
* 	(at your option) any later version.
*
* 	Dolphin is distributed in the hope that it will be useful,
* 	but WITHOUT ANY WARRANTY; without even the implied warranty of
* 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* 	GNU General Public License for more details.
*
* 	You should have received a copy of the GNU General Public License
* 	along with Dolphin. If not, see <http://www.gnu.org/licenses/>.
*
*
*    Description: presenting login form
*
*    Author: Niklas Odén
*
***********************************************************************************************/

//---------------------------------------------------------------------------------------------
//
//   Interception filter
//

if(!$frontDoor) 
	die('No direct access to pagecontroller allowed!');

//---------------------------------------------------------------------------------------------
//
//    needed files
//

require_once(TP_SQLPATH . 'config.php');

//---------------------------------------------------------------------------------------------
//
//    creating objects
//

$pc = new CPageController();

//---------------------------------------------------------------------------------------------
//
//    taking care of post variables
//

$username = isset($_POST['userName']) ? $_POST['userName'] : '';
$password = isset($_POST['userPwd']) ? $_POST['userPwd'] : '';
$loginRedirect = isset($_POST['loginRedirect']) ? $_POST['loginRedirect'] : '';

$silentLogin = $pc->SESSIONIsSetOrSetDefault('silentLogin');

global $gModule;

$redirectFailure = "?m={$gModule}&p=login";

//--------------------------------------------------------------------------------------------
//
//	preparing silent login
//

if(!empty($silentLogin)) {


$username = $pc->SESSIONIsSetOrSetDefault('silentAccount');
$password = $pc->SESSIONIsSetOrSetDefault('silentPassword');
$loginRedirect = $pc->SESSIONIsSetOrSetDefault('silentRedirect');
$redirectFailure = $pc->SESSIONIsSetOrSetDefault('silentRedirectFailure');

unset($_SESSION['silentAccount']);
unset($_SESSION['silentPassword']);
unset($_SESSION['silentRedirect']);
unset($_SESSION['silentRedirectFailure']);

}


//---------------------------------------------------------------------------------------------
//
//    preparing and performing query
//


$spAuthenticateUser = DBSP_PAuthenticateUser;
$spGetAccountInfo = DBSP_PGetAccountInfo;


$db = new CDatabaseController();
$mysqli = $db->connectToDatabase();

$username = $mysqli->real_escape_string($username);
$password = $mysqli->real_escape_string($password); 

$query = <<< EOD
	SET @pUserAccountOrEmail = '{$username}';
	SET @pPassword = '{$password}';
	call {$spAuthenticateUser}(@pUserAccountOrEmail, @pPassword, @pUserId, @pStatus);
	SELECT
		@pUserId AS userId,
		@pStatus AS logStatus;
EOD;



$res = $db->performDirectMultiQuery($query);
$db->getAndStoreResults($result);

$row = $result[3]->fetch_object();
$status = $row->logStatus;
$idUser = $row->userId;




//---------------------------------------------------------------------------------------------
//
//    use the result of the query to populate a session that shows that we are logged in
//                
require_once(TP_SOURCEPATH . 'FSessionDestroy.php');

session_start();// Must call it since we destroyed it above.
session_regenerate_id(); // To avoid problems


if($status == 0) {

$query = <<< EOD
	SET @pUserId = {$idUser};
	CALL {$spGetAccountInfo}(@pUserId);
EOD;

	$res = $db->performDirectMultiQuery($query);
	$db->getAndStoreResults($result);
	$row = $result[1]->fetch_object();

	$_SESSION['idUser']             = $idUser;
	$_SESSION['accountUser']         = $row->account;        
	$_SESSION['groupMemberUser']     = $row->groupId;

	$result[1]->close();

} else {

	$_SESSION['errorMessage'] = "Inloggningen misslyckades";
	$_SESSION['login'] = $redirectFailure;

}


$mysqli->close();

//---------------------------------------------------------------------------------------------
//
//    redirecting to another page
//

$redirect = isset($_SESSION['login']) ? $_SESSION['login'] : $loginRedirect;
unset($_SESSION['login']);

header('Location: ' . WS_SITELINK . "{$redirect}");
exit;

?>