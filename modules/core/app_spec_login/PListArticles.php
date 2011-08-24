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
*    Description: listing articles
*
*    Author: Niklas Odén
*
***********************************************************************************************/

//---------------------------------------------------------------------------------------------
//	interceptionfilter
//
//

$iFilter = new CInterceptionFilter();
$iFilter->FrontControllerIsVisited();
$iFilter->UserLoginStatus();

//---------------------------------------------------------------------------------------------
//
//	connecting required files
//

require_once(TP_SQLPATH . 'config.php');
require_once(TP_SQLPATH . 'CSQL.php');


//---------------------------------------------------------------------------------------------
//
//	taking care of get variables, setting necessary variables
//

$newArticle = isset($_GET['newArticle']) ? $_GET['newArticle'] : '';
$articleId = isset($_GET['articleId']) ? $_GET['articleId'] : 4;
$articleList = isset($_GET['articleList']) ? $_GET['articleList'] : '';

$user = isset($_SESSION['accountUser']) ? $_SESSION['accountUser'] : '';
$idUser = isset($_SESSION['idUser']) ? $_SESSION['idUser'] : '';

$spPListArticles = DBSP_PListArticles;
global $gModule;

if($articleList == "all") {
	$headline = "Artiklar i Dolphins arkiv";
} else {
	$headline = "Mina artiklar";
}

//---------------------------------------------------------------------------------------------
//
//   creating necessary objects
//

$artNav = new CNavigation();
$articleNav = $artNav->articleNavigation();
$db = new CDatabaseController();
$queryObject = new CSQL();


//---------------------------------------------------------------------------------------------
//
//   preparing and performing query
//

$query = "call {$spPListArticles}({$idUser}, '{$articleList}');";
$mysqli = $db->connectToDatabase();
$res = $db->performDirectQuery($query);

//---------------------------------------------------------------------------------------------
//
//    the content of the page
//

$centerBody = <<< EOD
		<h3>{$headline}</h3>
EOD;

while($row = $res->fetch_object()) {
	$articleId = $row->id;
	$title = $row->articleTitle;
	$text = $row->articleText;
	$articleDate = $row->articleDate;
	$articleModifyDate = $row->articleModifyDate;
	$author = $row->accountUser;
	
	if(!empty($articleModifyDate)) {
		$articleDate = $articleModifyDate;
	} 
	
	$centerBody .= <<< EOD
		<div class='showArticleList'>
			<div class='left'>
			<a href='?m=$gModule&amp;p=showmessage&amp;articleId={$articleId}' class='listNav' >{$title}</a>
			<br /><br />
			<p class='articleInfo'>Skriven av: {$author}, senast uppdaterad: {$articleDate}</p>
			</div>
			<div class='right'>
				<a href='?m=$gModule&amp;p=showmessage&amp;articleId={$articleId}' alt='' class='listNav'>Läs</a>
			</div>
			<div class='clear'></div>
		</div>
EOD;
}

$leftBody = "";
$rightBody = <<< EOD
	{$articleNav}

EOD;
$subHeader = <<< EOD
	<h1>Artiklar</h1>

EOD;
	
$mysqli->close();
//---------------------------------------------------------------------------------------------
//
//	printing out the page
//
$title = "Home";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody);

?>