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
*    Description: form for new message
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
//$iFilter->UserGroupStatus();

//---------------------------------------------------------------------------------------------
//
//	connecting required files
//

require_once(TP_SQLPATH . 'config.php');
require_once(TP_SQLPATH . 'CSQL.php');

//---------------------------------------------------------------------------------------------
//
//   creating necessary objects
//

$db = new CDatabaseController();
$queryObject = new CSQL();
$pc = new CPageController();


//---------------------------------------------------------------------------------------------
//
//	taking care of variables and constants
//

$articleId = isset($_GET['articleId']) ? $_GET['articleId'] : 0;
$idUser = $pc->SESSIONIsSetOrSetDefault('idUser');

$spDisplayTopicAndPosts = DBSP_PDisplayTopicAndPosts;
$spAttachedFiles = DBSP_PAttachedFiles;
$spGetAccountInfo = DBSP_PGetAccountInfo;

$attachments = "";
$download = "?m=core&amp;p=downloadp&amp;file=";

//---------------------------------------------------------------------------------------------
//
//   preparing and performing query
//

$result = ARRAY();
$query = <<< EOD
	SET @articleId = {$articleId};
	call {$spDisplayTopicAndPosts}({$articleId});
	call {$spAttachedFiles}('{$idUser}', '{$articleId}');
EOD;
$mysqli = $db->connectToDatabase();
$result = $db->performMultiQueryAndStore($query);
$row = $result[1]->fetch_object();
if($result[1]->num_rows == 0) {
	$title = "Ingen artikel";
	$text = "Det finns ännu ingen artikel registrerad i Dolphin.";
	$articleId = 0;
	
} else {
	$title = $row->title;
	$text = $row->content;
	$author = $row->username;
	$posts = $row->posts;
	$created = $row->created;
	$lastEdit = $row->lastEdit;
	$idUser = $row->idUser;
	$avatar = $row->avatar;
	$gravatar = $row->gravatar;
	$lastPost = $row->lastPost;
	$username = $row->username;
	$lastPostDate = $row->lastPostDate;
}
$result[1]->close();

$row=$result[2]->fetch_object();
if($result[2]->num_rows != 0) {
$lastAuthor = $row->lastAuthor;
} else {
	$lastAuthor = "";
}
$result[2]->close();

if($result[5]->num_rows > 0) {

	$attachments = <<< EOD
		<h3>Bilagor:</h3>
EOD;
	while($row = $result[5]->fetch_object()) {
		$idFile = $row->idFile;
		$name = $row->name;
		$uniqueName = $row->uniqueName;
		$path = $row->path;
		$size = $row->size;
		$mimetype = $row->mimetype;
		
		$kbSize = $size/1000;
		$linkName = $name . " ( " . $kbSize . " kb )";
		
		$attachments .= <<< EOD
			<a href='{$download}{$uniqueName}'>{$linkName}</a>
			<br />
EOD;
	}
}
//---------------------------------------------------------------------------------------------
//
//    the content of the page
//




$editLink = <<< EOD
	<a href='?m=tuna&amp;p=edittopic&amp;articleId={$articleId}&amp;user={$idUser}'><img src='images/edit.png' alt='editicon' /></a>
EOD;

$editLink = ($iFilter->UserIsAdminOrCurrent($idUser)) ? $editLink : '';

$articleInfo = <<< EOD
	<h3>Om aktuellt ämne</h3>
	<hr />
	<p class='topicsListText'>Skapat av {$username} <br />{$created} <br /><br />
	 Antal poster i ämnet:  {$posts}<br /><br />
	Senaste inlägget av {$lastAuthor}<br />
	{$lastPostDate}</p>

EOD;

$avatarOrGravatar = $pc->CreateGravatarLink($gravatar, 80);
$avatar = ($avatarOrGravatar) ? $avatarOrGravatar : $avatar;

$centerBody = <<< EOD
	<div class='articleFrameHead'>
		<div class='left'>
			<img src='{$avatar}' height=80px;/>
			<p class='articleInfo'>{$author}<br />{$lastEdit}</p>
		</div>
		<div class='leftbox'>
				<p>{$text}</p>
		</div>
		<div class='right'>
			{$editLink}
			
		</div>
	</div>
EOD;

//-------------------------------------get post connected to current topic

while($row = $result[3]->fetch_object()) {

$postId = $row->postId;
$avatar = $row->avatar;
$gravatar = $row->gravatar;
$idUserPost = $row->idUserPost;
$editLink = <<< EOD
	<a href='?m=tuna&amp;p=edittopic&amp;articleId={$articleId}&amp;postId={$postId}&amp;articleTitle={$title}&amp;comment=comment&amp;user={$idUser}'><img src='images/edit.png' alt='editicon' /></a>
EOD;

$editLink = ($iFilter->UserIsAdminOrCurrent($idUserPost)) ? $editLink : '';

$avatarOrGravatar = $pc->CreateGravatarLink($gravatar, 60);
$avatar = ($avatarOrGravatar) ? $avatarOrGravatar : $avatar;


$centerBody .= <<< EOD
	<div class='clear'></div>
	<div class='articleFrame'>
		<div class='left'>
			<img src='{$avatar}' alt='userimage' height=60px;/>
			<p class='articleInfo'>{$row->username}<br />{$row->created}</p>
		</div>
		<div class='leftbox'>
			<p>{$row->content}</p>
		</div>
		<div class='right'>
			{$editLink}
		</div>
	</div>
EOD;

}

$centerBody .= <<< EOD
	<div class='clear'></div>
	<div class='articleFrame'>
		<div class='right'>
		<a href='?m=tuna&amp;p=newtopic&amp;articleId={$articleId}&amp;articleTitle={$title}' class='comment' >Svara/kommentera</a>
		</div>
	<div style='width: 100%;'>
		{$attachments}
	</div>
	</div>
EOD;

$leftBody = "";

$rightBody = <<< EOD
	{$articleInfo}

EOD;

$subHeader = <<< EOD
	<h1 class='topic'>{$title}</h1>
EOD;

$result[3]->close();

/*
if(USER_GRAVATAR && (!empty($gravatar) && $gravatar != NULL)) {

	$query = <<< EOD
		CALL {$spGetAccountInfo}({$idUser});
EOD;
	$result = $db->performMultiQueryAndStore($query);
	$row = $result[0]->fetch_object();
}*/
$mysqli->close();
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