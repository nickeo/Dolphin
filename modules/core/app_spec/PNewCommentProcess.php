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
*    Description: processing a new comment
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
//$iFilter->UserLoginStatus();

//---------------------------------------------------------------------------------------------
//
//    taking care of post and post variables
//

$messageId = isset($_POST['messageId']) ? $_POST['messageId'] : '';
$newComment = isset($_POST['newComment']) ? $_POST['newComment'] : '';
$commentTitle = isset($_POST['commentTitle']) ? $_POST['commentTitle'] : '';
$commentSignature = isset($_POST['commentSignature']) ? $_POST['commentSignature'] : '';
$commentMail = isset($_POST['commentMail']) ? $_POST['commentMail'] : '';

//---------------------------------------------------------------------------------------------
//
//	simple e-mail test
//

$control = stripos($commentMail, "@");

if($control=== false || strlen($commentMail)<5)
			die("Varför anger du en felaktig emailadress? Fegis?");

//---------------------------------------------------------------------------------------------
//
//    connecting to database
//

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

if(mysqli_connect_error()) {
    echo "Connection failed: ". mysqli_connect_error()."<br/>";
    exit();
}

//---------------------------------------------------------------------------------------------
//
//	preparing and performing query
//

$newComment = $mysqli->real_escape_string($newComment);
$commentTitle = $mysqli->real_escape_string($commentTitle);
$commentSignature = $mysqli->real_escape_string($commentSignature);
$commentMail = $mysqli->real_escape_string($commentMail);


$query = <<< EOD
	INSERT INTO new_foogler_Comment (commentTitle, comment, commentDate, comment_idPost, commentSign, commentMail)
	VALUES ('{$commentTitle}', '{$newComment}', NOW(), '{$messageId}', '{$commentSignature}', '{$commentMail}');
EOD;

$res = $mysqli->query($query)
		or die('Could not query database');

//---------------------------------------------------------------------------------------------
//
//    the content of the page
//


$leftBody = <<< EOD
    
    
EOD;

$centerBody = "";

$rightBody = <<<EOD
EOD;
//echo $body;

$mysqli->close();

header('Location: ' . WS_SITELINK . "?p=showmessage");
exit;


//---------------------------------------------------------------------------------------------
//
//	printing out the page
//

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->printHTMLHeader(WS_TITLE);
$page->printPageHeader();
$page->printPageBody($leftBody, $centerBody, $rightBody);
$page->printPageFooter();

?>