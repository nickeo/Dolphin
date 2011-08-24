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
*    Description: showing message
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
//$iFilter->UserGroupStatus();

//---------------------------------------------------------------------------------------------
//
//	connecting required files
//

//require_once(TP_SQLPATH . 'config.php');
require_once(TP_SQLPATH . 'CSQL.php');


//---------------------------------------------------------------------------------------------
//
//	taking care of get variables
//

$articleId = isset($_GET['articleId']) ? $_GET['articleId'] : 0;
$idUser = isset($_SESSION['idUser']) ? $_SESSION['idUser'] : '';

$spPDisplayArticle = DBSP_PDisplayArticle;

//---------------------------------------------------------------------------------------------
//
//   creating necessary objects
//

$artNav = new CNavigation();
$db = new CDatabaseController();
$queryObject = new CSQL();
$pc = new CPageController();

//---------------------------------------------------------------------------------------------
//
//   preparing and performing query
//


$result = ARRAY();
$query = "call {$spPDisplayArticle}(@pGrantRights, {$articleId}, {$idUser}); SELECT @pGrantRights AS editArticle;";
$mysqli = $db->connectToDatabase();
$res = $db->performDirectMultiQuery($query);
$db->getAndStoreResults($result);
$row = $result[0]->fetch_object();
if($result[0]->num_rows == 0) {
	$title = "Ingen artikel";
	$text = "Det finns ännu ingen artikel registrerad i Dolphin.";
	$articleId = 0;
	$author = "";
	$articleDate ="";
} else {
	$articleId = $row->id;
	$title = $row->articleTitle;
	$text = $row->articleText;
	$articleDate = $row->articleDate;
	$articleModifyDate = $row->articleModifyDate;
	$author = $row->accountUser;

if(!empty($articleModifyDate)) {
		$articleDate = $articleModifyDate;
	} 

}
$result[0]->close();
$row = $result[2]->fetch_object();
$editor = $row->editArticle;

$mysqli->close();


//---------------------------------------------------------------------------------------------
//
//    the content of the page
//

$articleNav = $artNav->articleNavigation($articleId, $editor);

$centerBody = <<< EOD
	<div class='articleFrame'>
		<h3>{$title}</h3><br />
		{$text}
		<br /><br/>
		<p class='articleInfo'>Skriven av: {$author}, senast uppdaterad: {$articleDate}</p>
		
	</div>
EOD;
$leftBody = "";
$rightBody = <<< EOD
	{$articleNav}

EOD;
$subHeader = <<< EOD
	<h1>Artiklar</h1>

EOD;
	

//---------------------------------------------------------------------------------------------
//
//	printing out the page
//
$title = "Artiklar";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody);

?>