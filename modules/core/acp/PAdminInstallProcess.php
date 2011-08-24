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
*    Description: installing process - for Dolphin
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
/*
$iFilter = new CInterceptionFilter();
$iFilter->FrontControllerIsVisited();
$iFilter->UserLoginStatus();
$iFilter->UserGroupStatus();
*/
require_once(TP_SQLPATH . 'config.php');

// -------------------------------------------------------------------------------------------
//
// creating objects
//

$pc = new CPageController();
unset($_SESSION['redirect']);
// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//

$account = $pc->POSTIsSetOrSetDefault('account');
$files = $pc->POSTIsSetOrSetDefault('files');
$article = $pc->POSTIsSetOrSetDefault('article');
$tunatalk = $pc->POSTIsSetOrSetDefault('tunatalk');
$custom = $pc->POSTIsSetOrSetDefault('custom');

$queryCode = "";
$queryInfo = "";

if($account == "account" || $custom == "custom") {
	require_once(TP_SQLPATH . 'SQLCoreAccount.php');
	$queryCode .= $queryAccount;
	$queryInfo .= "<h2 class='installh2'>Installation av Core-Account</h2><pre>" . $queryAccount . "</pre><br />";
} else {
	$queryInfo .= "<h2 class='installh2'>Installation av Core-Account</h2><br /><p class='alertInstall'>Disabled</p><br />";
}
if ($files == "files" || $custom == "custom") {
	require_once(TP_SQLPATH . 'SQLCoreFiles.php');
	$queryCode .= $queryFile;
	$queryInfo .= "<h2 class='installh2'>Installation av Core-Files</h2><pre>" . $queryFile . "</pre><br />";
} else {
	$queryInfo .= "<h2 class='installh2'>Installation av Core-Files</h2><br /><p class='alertInstall'>Disabled</p><br />";
}
if ($article == "article" || $custom == "custom") {
	require_once(TP_SQLPATH . 'SQLCoreArticle.php');
	$queryCode .= $queryArticle;
	$queryInfo .= "<h2 class='installh2'>Installation av Core-Article</h2><pre>" . $queryArticle . "</pre><br />";
} else {
	$queryInfo .= "<h2 class='installh2'>Installation av Core-Article</h2><br /><p class='alertInstall'>Disabled</p><br />";
}
if ($tunatalk == "tunatalk" || $custom == "custom") {
	require_once(TP_SQLPATH . 'SQLTunatalk.php');
	$queryCode .= $queryTunatalk;
	$queryInfo .= "<h2 class='installh2'>Installation av Tunatalk</h2><pre>" . $queryTunatalk . "</pre><br />";
} else {
	$queryInfo .= "<h2 class='installh2'>Installation av Tunatalk</h2><br /><p class='alertInstall'>Disabled</p><br />";
}


$db = new CDatabaseController();

$mysqli = $db->connectToDatabase();
$res = $db->performDirectMultiQuery($queryCode);
$mysqli->close();


$centerBody = <<< EOD
<br />
{$queryInfo}
<br />
EOD;

$subHeader = "<div class='adRightTurquoise'><a href='' class='big'>Kontrollpanel</a></div><h1>Installation av databas</h1>";

$leftBody = "";

$rightBody = "";

//---------------------------------------------------------------------------------------------
//
//	printing out the page
//
$title = "Installation";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody);

?>