<?php
/**********************************************************************************************
//
//	Dolphin , software to build webbapplications.
//	Copyright (C) 2011 Niklas Odén (niklasoden@hotmail.com)
//
// 	This file is part of Dolphin.
//
// 	Dolphin is free software: you can redistribute it and/or modify
// 	it under the terms of the GNU General Public License as published by
// 	the Free Software Foundation, either version 3 of the License, or
// 	(at your option) any later version.
//
// 	Dolphin is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// 	GNU General Public License for more details.
//
// 	You should have received a copy of the GNU General Public License
// 	along with Dolphin. If not, see <http://www.gnu.org/licenses/>.
//
//
//	Description: presenting login form
//
//   Author: Niklas Odén
//
***********************************************************************************************/

//---------------------------------------------------------------------------------------------
//
//   Interception filter
//

if(!$frontDoor) 
	die('No direct access to pagecontroller allowed!');


//---------------------------------------------------------------------------------------------
//
//   creating objects
//

$db = new CDatabaseController();
$mysqli = $db->connectToDatabase();


//---------------------------------------------------------------------------------------------
//
//    taking care of post variables
//

$username = isset($_POST['userName']) ? $_POST['userName'] : '';
$password = isset($_POST['userPwd']) ? $_POST['userPwd'] : '';
$passwordConfirm = isset($_POST['confPwd']) ? $_POST['confPwd'] : '';
$group = 'usr';
$loginRedirect = isset($_POST['loginRedirect']) ? $_POST['loginRedirect'] : '';

global $gModule;

//---------------------------------------------------------------------------------------------
//
//    checking input and password
//

$username = $mysqli->real_escape_string($username);
$password = $mysqli->real_escape_string($password);


$pc = new CPageController();

$failureRedirect = "?m={$gModule}&p=createa";
$pc->CheckPassword($password, $passwordConfirm, $failureRedirect);

$_SESSION['errorMessage'] = "";

//---------------------------------------------------------------------------------------------
//
//    preparing and performing query
//


//
// Check the CAPTCHA
//
if(USE_RECAPTCHA == true) {
	$captcha = new CCaptcha();
	if(!$captcha->CheckAnswer()) {
		$_SESSION['errorMessage'] = "Captcha: ej korrekta angivna ord!";
		unset($_SESSION['redirect']);
		header('Location: ' . WS_SITELINK . "?p=createa");
		exit;
		
}
}

$result = ARRAY();
$PCreateAccount = DBSP_PCreateAccount;
$pwdHash = DB_PASSWORDHASHING;


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

//---------------------------------------------------------------------------------------------
//
//    redirecting to another page
//


if($uniqueStatus == 1) {
	$stat = "failure";
	$redirect = $failureRedirect;
	$_SESSION['errorMessage'] = "Valt användarnamn existerar redan!";
	unset($_SESSION['redirect']);
} else {
	$stat = "success";
	$_SESSION['silentAccount'] = $username;
	$_SESSION['silentPassword'] = $password;
	$_SESSION['silentRedirect'] = "?m={$gModule}&p=accounts";
	$_SESSION['silentRedirectFailure'] = "?m={$gModule}&p=createa";
	$_SESSION['silentLogin'] = "silentLogin";
	$redirect = "?m={$gModule}&p=loginp";
}
header('Location: ' . WS_SITELINK . "{$redirect}");
exit;

?>