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
*    Description: process handling new account
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

$iFilter = new CInterceptionFilter();
$iFilter->FrontControllerIsVisited();
$iFilter->UserLoginStatus();
$iFilter->UserGroupStatus();

//---------------------------------------------------------------------------------------------
//
//	new instance
//

$pc = new CPageController();

//---------------------------------------------------------------------------------------------
//
//    taking care of post variables
//

$username = $pc->POSTIsSetOrSetDefault('userName');
$password = $pc->POSTIsSetOrSetDefault('userPwd');
$passwordConfirm = $pc->POSTIsSetOrSetDefault('confPwd');
$group = $pc->POSTIsSetOrSetDefault('group');
$loginRedirect = isset($_POST['loginRedirect']) ? $_POST['loginRedirect'] : '';

global $gModule;

$failureRedirect = "?m={$gModule}&p=createaccount";
$successRedirect = "?m={$gModule}&p=editaccount";

//---------------------------------------------------------------------------------------------
//
//    checking password
//



$pc->CheckPassword($password, $passwordConfirm, $failureRedirect);

$_SESSION['errorMessage'] = "";

//---------------------------------------------------------------------------------------------
//
//    preparing and performing query
//


$result = ARRAY();
$PCreateAccount = DBSP_PCreateAccount;
$pwdHash = DB_PASSWORDHASHING;

$db = new CDatabaseController();
$mysqli = $db->connectToDatabase();

$username = $mysqli->real_escape_string($username);
$password = $mysqli->real_escape_string($password);

$query = <<< EOD
	CALL {$PCreateAccount}(@pUserId, '{$username}', '{$group}', '{$password}', '{$pwdHash}', @pStatus);
	SELECT @pUserId AS userId,
	@pStatus AS uniqueStatus;
EOD;

$res = $db->performDirectMultiQuery($query);
$db->getAndStoreResults($result);
$row = $result[1]->fetch_object();
$userId = $row->userId;
$uniqueStatus = $row->uniqueStatus;


$mysqli->close();
/*
//---------------------------------------------------------------------------------------------
//
//    redirecting to another page
//

$redirect = isset($_SESSION['login']) ? $_SESSION['login'] : $loginRedirect;

/*if(isset($_POST['loginRedirect']) && $_POST['loginRedirect'] !="") {        // redirect via post
    $redirect= $_POST['loginRedirect'];
}*/

/*if(isset($sessionRedirect){                // redirect via session->vanligt variabel
    $redirect = $sessionRedirect;
}*/
/*
header('Location: ' . WS_SITELINK . "{$redirect}");
exit;
*/

if($uniqueStatus == 1) {
	$stat = "failure";
	$_SESSION['errorMessage'] = "Användarnamn existerar redan!";
	header('Location: ' . WS_SITELINK . "{$failureRedirect}");
	exit;
} else {
	$stat = "success";
	header('Location: ' . WS_SITELINK . "{$successRedirect}&id={$userId}");
	exit;
}

?>