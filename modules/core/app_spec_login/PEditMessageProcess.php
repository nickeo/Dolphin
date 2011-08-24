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
*    Description: processing an edited article
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
//    taking care of post and session variables
//

$articleTitle = isset($_POST['articleTitle']) ? $_POST['articleTitle'] : '';
$articleText = isset($_POST['articleText']) ? $_POST['articleText'] : '';
$articleId = isset($_POST['articleId']) ? $_POST['articleId'] : '';
$editArticle = isset($_POST['editArticle']) ? $_POST['editArticle'] : '';

$user = isset($_SESSION['accountUser']) ? $_SESSION['accountUser'] : '';
$idUser = isset($_SESSION['idUser']) ? $_SESSION['idUser'] : '';
$redirectArticleId = $articleId;

$spPUpdateArticle = DBSP_PUpdateArticle;

//--------------------------------------------------------------------------------------------
//
//	clean up html-tags
//
$tagsAllowed = '<h1><h2><h3><h4><h5><h6><p><b><a><br><i><em><li><ol><ul>';

$articleTitle = strip_tags($articleTitle, $tagsAllowed);
$articleText = strip_tags($articleText, $tagsAllowed);

// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//

$queryObject = new CSQL();
$db = new CDatabaseController();
$mysqli = $db->connectToDatabase();

if($editArticle == "Ta bort") {
	$editOrDelete = 1;
	$redirectArticleId = 0;
} else {
	$editOrDelete = 0;
}

$query = "call {$spPUpdateArticle}({$editOrDelete}, {$articleId}, '{$articleTitle}', '{$articleText}', '{$idUser}');";
$res = $db->performDirectMultiQuery($query);
$mysqli->close();

//--------------------------------------------------------------------------------------------------
//
//	redirecting
//

header('Location: ' . WS_SITELINK . "?p=showmessage&articleId={$redirectArticleId}");
exit;

?>