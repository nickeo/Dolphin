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
*    Description: taking care of account-updating process
*
*    Author: Niklas Odén
*
***********************************************************************************************/


//---------------------------------------------------------------------------------------------
//
//    pagecontroller
//

$pc = new CPageController();

//---------------------------------------------------------------------------------------------
//
//    taking care of post variables
//

$submit = $pc->POSTisSetOrSetDefault('submit');
$username = $pc->POSTisSetOrSetDefault('username');


$avatar = isset($_POST['avatar']) ? $_POST['avatar'] : '';
$gravatar = isset($_POST['gravatar']) ? $_POST['gravatar'] : '';
$loginRedirect = isset($_POST['loginRedirect']) ? $_POST['loginRedirect'] : '';

$failRedirect = "?p=accounts";
$redirect = "?p=accounts";
$userId = $_SESSION['idUser'];

//---------------------------------------------------------------------------------------------
//
//    preparing and performing query
//
$result = ARRAY();
$spUpdatePassword = DBSP_PUpdatePassword;
$spUpdateEmail = DBSP_PUpdateEmail;
$spUpdateAvatar = DBSP_PUpdateAvatar;
$spUpdateGravatar = DBSP_PUpdateGravatar;

$db = new CDatabaseController();
$mysqli = $db->connectToDatabase();



//---------------------------------------------------------------------------------------------
//
//    changing password
//

if($submit == "changepwd") {

$password = isset($_POST['userPwd']) ? $_POST['userPwd'] : '';
$passwordConfirm = isset($_POST['confPwd']) ? $_POST['confPwd'] : '';

$pc->CheckPassword($password, $passwordConfirm, $failRedirect);

$password = $mysqli->real_escape_string($password);

$query = <<< EOD
	CALL {$spUpdatePassword}('{$userId}', '{$password}');
EOD;

$res = $db->performDirectMultiQuery($query);

header('Location: ' . WS_SITELINK . "{$redirect}");

//---------------------------------------------------------------------------------------------
//
//    changing mail
//

} else if($submit == "changemail") {
	$email = $pc->POSTisSetOrSetDefault('email');
	$email = $mysqli->real_escape_string($email);
	
	$query = <<< EOD
	CALL {$spUpdateEmail}('{$userId}', '{$email}', @pRowsAffected);
	SELECT @pRowsAffected AS rowsAffected;
EOD;
	
	$res = $db->performDirectMultiQuery($query);
	$db->getAndStoreResults($result);
	$row = $result[1]->fetch_object();
	
	if($row->rowsAffected == 1) {
	
		$mail = new CMail();
		$to = $email;
		$from = "niod09@student.bth.se";
		$subject = "Ny mailadress registrerad";
		$message = <<< EOD
Välkommen,
Din nya mailadress är registrerad.

MVH
Dolphin

EOD;
		$success = $mail->SendMail($to, $from, $subject, $message);
		
		if($success) {
			$_SESSION['errorMessage'] = "Lyckades med att skicka mail till " . $email;
		} else {
			$_SESSION['errorMessage'] = "Misslyckades med att skicka mail till " . $email;
		}
	
	}

header('Location: ' . WS_SITELINK . "{$redirect}");
exit;
//---------------------------------------------------------------------------------------------
//
//    changing avatar
//

} else if($submit == "changeavatar") {
	$avatar = $pc->POSTisSetOrSetDefault('avatar');
	$avatar = $mysqli->real_escape_string($avatar);
	
	$query = <<< EOD
	CALL {$spUpdateAvatar}('{$userId}', '{$avatar}');
EOD;

$res = $db->performDirectMultiQuery($query);

header('Location: ' . WS_SITELINK . "{$redirect}");

} else if($submit == "changegravatar") {
	$gravatar = $pc->POSTisSetOrSetDefault('gravatar');
	$gravatar = $mysqli->real_escape_string($gravatar);
	
	$query = <<< EOD
	CALL {$spUpdateGravatar}('{$userId}', '{$gravatar}');
EOD;

$res = $db->performDirectMultiQuery($query);

header('Location: ' . WS_SITELINK . "{$redirect}");

} else {
	
}


//---------------------------------------------------------------------------------------------
//
//    use the result of the query to populate a session that shows that we are logged in
//                
/*
session_start();// Must call it since we destroyed it above.
session_regenerate_id(); // To avoid problems


if($res->num_rows === 1) {
$_SESSION['idUser']             = $row->id;
$_SESSION['accountUser']         = $row->account;        
$_SESSION['groupMemberUser']     = $row->groupid; 
} else {
$_SESSION['errorMessage'] = "Inloggningen misslyckades";
$_SESSION['login'] = "?p=login";
}

*/

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
//echo $_SESSION['errorMessage'];
echo $submit . "<br />";
echo $success . "<br />";
echo $email . "<br />";
echo $headers . "<br />";
echo $message . "<br />";
//die('Submit action not supported');
?>