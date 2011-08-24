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
*    Description: deleting topic with all its post, or a single post
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

$user = isset($_SESSION['accountUser']) ? $_SESSION['accountUser'] : '';
$idUser = isset($_SESSION['idUser']) ? $_SESSION['idUser'] : '';

$articleId = isset($_GET['articleId']) ? $_GET['articleId'] : 0;
$parentId = isset($_GET['parentId']) ? $_GET['parentId'] : 0;
$postId = isset($_GET['postId']) ? $_GET['postId'] : 0;

if($postId != 0) {
	$parentId = $articleId;
	$articleId = $postId;
}
/*
echo $parentId . " <h3>parentId</h3> <br />";
echo $articleId;*/
// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//

$spDeletePost = DBSP_PDeletePost;

$db = new CDatabaseController();
$query = <<< EOD
	SET @articleId = {$articleId};
	SET @parentId = {$parentId};
	call {$spDeletePost}(@articleId, @parentId); 
EOD;
$mysqli = $db->connectToDatabase();
$res = $db->performDirectMultiQuery($query);
$db->getAndStoreResults($result);

$mysqli->close();


//---------------------------------------------------------------------------------------------
//
//    redirecting
//



header('Location: ' . WS_SITELINK . "?m=tuna&p=topics");
exit;

?>