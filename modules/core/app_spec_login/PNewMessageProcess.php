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
*    Description: processing a new message
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
//    taking care of post and post variables
//

$articleTitle = isset($_POST['articleTitle']) ? $_POST['articleTitle'] : '';
$articleText = isset($_POST['articleText']) ? $_POST['articleText'] : '';
$user = isset($_SESSION['accountUser']) ? $_SESSION['accountUser'] : '';
$idUser = isset($_SESSION['idUser']) ? $_SESSION['idUser'] : '';

$spPCreateNewArticle = DBSP_PCreateNewArticle;
$articleId="";

//--------------------------------------------------------------------------------------------
//
//	clean up html-tags
//
$tagsAllowed = '<h1><h2><h3><h4><h5><h6><p><b><a><br><i><em><li><ol><ul>';

//$articleTitle = strip_tags($articleTitle, $tagsAllowed);
//$articleText = strip_tags($articleText, $tagsAllowed);

// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//

$tblArticle = DBT_Article;

$db = new CDatabaseController();
$query = "call {$spPCreateNewArticle}(@articleId,'{$articleTitle}', '{$articleText}', '{$idUser}'); SELECT @articleId AS id;";
$mysqli = $db->connectToDatabase();
$res = $db->performDirectMultiQuery($query);

$db->getAndStoreResults($result);
$row = $result[1]->fetch_object();
$articleId = $row->id;

$mysqli->close();


//---------------------------------------------------------------------------------------------
//
//    redirecting
//


header('Location: ' . WS_SITELINK . "?p=showmessage&articleId={$articleId}");
exit;

?>