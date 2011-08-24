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
*    Description: deleting a comment
*
*    Author: Niklas Odén
*
***********************************************************************************************/
/*
//---------------------------------------------------------------------------------------------
//
//   Interception filter
//

if(!$frontDoor) 
	die('No direct access to pagecontroller allowed!');

$iFilter = new CInterceptionFilter();
$iFilter->FrontControllerIsVisited();
$iFilter->UserLoginStatus();

//---------------------------------------------------------------------------------------------
//
//    taking care of post and post variables
//

$commentId = isset($_GET['commentId']) ? $_GET['commentId'] : '';


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
		new_foogler_Comment
	WHERE
		id = '{$commentId}';
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