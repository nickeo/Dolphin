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
*    Description: deleting a message
*
*    Author: Niklas Odén
*
***********************************************************************************************/
/*
if(!isset($frontDoor)) {
		die('No directaccess to pagecontroller allowed');
}

if(!isset($_SESSION['accountUser'])) {
	header("Location: ?p=home");
}

//---------------------------------------------------------------------------------------------
//
//    error reporting on
//
error_reporting(E_ALL);


//---------------------------------------------------------------------------------------------
//
//    taking care of post and post variables
//

$messageId = isset($_GET['messageId']) ? $_GET['messageId'] : '';
$userControl = isset($_SESSION['accountUser']) ? $_SESSION['accountUser'] : '';

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


$query = <<< EOD
	DELETE FROM
		new_foogler_Post
	WHERE
		idPost = '{$messageId}'
	AND
		post_username = '{$userControl}';
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
*/
?>