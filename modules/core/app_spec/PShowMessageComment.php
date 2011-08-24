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
*    Description: showing a message and its comments
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

//---------------------------------------------------------------------------------------------
//
//    taking care of post and post variables
//

$messageId = isset($_GET['messageId']) ? $_GET['messageId'] : '';
$newPost = isset($_POST['newPost']) ? $_POST['newPost'] : '';

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
	SELECT message, messageTitle, messageDate, username, U.name from new_foogler_User as U
left outer join new_foogler_Post  as P on
U.username = P.post_username
WHERE
	idPost = '{$messageId}';
EOD;

$query .= <<< EOD
	SELECT
		id, commentTitle, comment, commentDate, commentSign, commentMail
	FROM
		new_foogler_Comment
	WHERE
		comment_idPost = '{$messageId}';
EOD;

$query .= "SELECT username, name FROM new_foogler_User;";
$query .= "SELECT idPost, messageTitle, post_username FROM new_foogler_Post
			ORDER BY messageDate DESC
			LIMIT 10;";


$res = $mysqli->multi_query($query)
		or die('Could not query database');

$res = $mysqli->store_result()
		or die("Failed to retrive result from query."); 

//---------------------------------------------------------------------------------------------
//
//    the content of the page
//



$messages = "";

$row = $res->fetch_object();

if(isset($_SESSION['accountUser']) && $_SESSION['accountUser'] == $row->username) {
	$user = <<< EOD
	<a href='?p=deletecomment
EOD;
} else {
	$user = "";
}

$messages .= <<< EOD
	<div class='messageframe'>
		<div class='messagehead'>
			<div>
				<h3 class='color' >{$row->messageTitle}</h3>
			</div>
		</div>
		<div class='clear'></div>
			<div class='message'>
				{$row->message}
			</div>
		<div class='messageinfo'>
			<p>{$row->messageDate} av: {$row->name}</p>
		</div>
	</div>
	<div class='clear'></div>
	<hr class='separator' />
	<hr class='separator' />
EOD;
$res->close();

// -------------------------------------------------------------------------------------------
//
// Show the results of NEXT the query
//
($mysqli->next_result() && $res = $mysqli->store_result())
                        or die("Failed to retrieve result from query"); 
$numRows = $res->num_rows;

if($numRows != 0) {
$messages .= "<p>Det här inlägget har {$numRows} kommentarer</p>";
}

while($row = $res->fetch_object()) {

if($user!="") {
	$commentDelete = $user."&amp;commentId={$row->id}'>Ta bort</a>";
} else {
	$commentDelete = "";
}

//------------------------------------------ preparing alias as first part of e-mail adress
$pos = stripos($row->commentMail, "@");
$alias =	substr($row->commentMail, 0, $pos);

$messages .= <<< EOD
	<div class='messageframe'>
		<div class='messagehead'>
			<div>
				<p class='color' >{$row->commentTitle}</p>
			</div>
		</div>
		<div class='clear'></div>
			<div class='message'>
				{$row->comment}
			</div>
		<div class='messageinfo'>
			<p>{$row->commentDate} Av: {$row->commentSign} &nbsp;&nbsp;Alias: {$alias} {$commentDelete}</p>
		</div>
	</div>
	<div class='clear'></div>
	<hr class='separator2' />
	
EOD;

}



$leftBody = <<< EOD
    {$messages}
    
EOD;

$centerBody = "";

$res->close();

// -------------------------------------------------------------------------------------------
//
// Show the results of NEXT the query
//
($mysqli->next_result() && $res = $mysqli->store_result())
                        or die("Failed to retrieve result from query"); 

if(isset($_SESSION['accountUser'])) {
$writeMessage = <<< EOD
	<a href='?p=newmessage' title='Nytt meddelande' ><div class='newmessage'></div></a>
EOD;
} else {
$writeMessage = "";
}

$rightInfo = <<< EOD
		<a href='http://www.student.bth.se/~niod09/dbwebb/projekt/RSS/RSSFeed.php'><img src='images/rss_logo2.jpg' border = '0' alt='' />&nbsp;&nbsp;</a>
		<a href='?p=showmessage&amp;layout={$_SESSION['officialOrPersonal']}'><img src='images/fooglerF2.png' border = '0' title='Ändra layout' alt='' />&nbsp;&nbsp;</a>
		{$writeMessage}
		<hr class='separator' />
		<h4>Medarbetare</h4>
		
EOD;

while($row = $res->fetch_object()) {
$rightInfo .= <<< EOD
	<p><a href='?p=showmessage&amp;author={$row->username}'>{$row->name}</a></p>
EOD;
}

$rightInfo .= <<< EOD
	<p><a href='?p=showmessage'>Visa alla</a></p>

EOD;

$res->close();

// -------------------------------------------------------------------------------------------
//
// Show the results of NEXT the query
//
($mysqli->next_result() && $res = $mysqli->store_result())
                        or die("Failed to retrieve result from query"); 

$rightInfo .= <<< EOD
		<hr class='separator' />
		<h4>Senaste Inlägg</h4>
EOD;

while($row = $res->fetch_object()) {
$rightInfo .= <<< EOD
	<p><a href='?p=message&amp;messageId={$row->idPost}'>{$row->messageTitle}</a></p>
EOD;
}

$rightBody = <<<EOD
	{$rightInfo}

EOD;
//echo $body;

$res->close();
$mysqli->close();

//---------------------------------------------------------------------------------------------
//
//	printing out the page
//

require_once('src/CHTMLPage.php');
$page = new CHTMLPage($layout);

$page->printHTMLHeader(WS_TITLE);
$page->printPageHeader();
$page->printPageBody($leftBody, $centerBody, $rightBody);
$page->printPageFooter();

?>