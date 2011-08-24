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

$articleId = isset($_POST['articleId']) ? $_POST['articleId'] : 0;
$parentId = isset($_POST['parentId']) ? $_POST['parentId'] : 0;

$jsRedirect = isset($_POST['jsRedirect']) ? $_POST['jsRedirect'] : 0;
$jsRedirectUrl = isset($_POST['jsRedirectUrl']) ? $_POST['jsRedirectUrl'] : "";
$publish = isset($_POST['saveOrPublish']) ? $_POST['saveOrPublish'] : "save";

//$publish = "publish";
$success = $jsRedirect;

$debug = $articleId;
//--------------------------------------------------------------------------------------------
//
//	clean up html-tags
//
$tagsAllowed = '<h1><h2><h3><h4><h5><h6><p><b><strong><a><br><i><em><li><ol><ul><embed><object><iframe>';

$articleTitle = strip_tags($articleTitle, $tagsAllowed);
$articleText = strip_tags($articleText, $tagsAllowed);

// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//

$spCreateOrUpDateTopic = DBSP_PCreateOrUpdateTopic;

$db = new CDatabaseController();
$query = <<< EOD
	SET @pTopicId = {$articleId};
	SET @pParentId = {$parentId};
	call {$spCreateOrUpDateTopic}(@pTopicId, @pParentId, '{$articleTitle}', '{$articleText}', '{$idUser}', '{$publish}'); 
	SELECT 
		@pTopicId AS id,
		@pParentId AS parentPost,
		NOW() AS timestamp;
EOD;
$mysqli = $db->connectToDatabase();
$res = $db->performDirectMultiQuery($query);

$db->getAndStoreResults($result);
$row = $result[3]->fetch_object();
$articleId = $row->id;
$parentId = $row->parentPost;
$timestamp = $row->timestamp;
$mysqli->close();


//---------------------------------------------------------------------------------------------
//
//    redirecting
//
//$success = 'json';

//$articleId = ($parentId != 0) ? $parentId : $articleId;
/*
if($jsRedirect == 'true') {
	$redirect = $jsRedirectUrl;
} else {
$articleId = ($parentId != 0) ? $parentId : $articleId;
	$redirect = "?m=tuna&p=showtopic&articleId={$articleId}";
}*/


if($success = 'json') {
$json = <<< EOD
{
"articleId": {$articleId},
"parentId": {$parentId},
"timestamp": "{$timestamp}",
"debug": "{$debug}"
}
EOD;

echo $json;

} else {
header('Location: ' . WS_SITELINK . $redirect);
}
exit;


?>