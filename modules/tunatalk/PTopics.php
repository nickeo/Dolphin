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
*    Description: Showing TunaTalks latest topics
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
//	taking care of get variables
//

$articleId = isset($_GET['articleId']) ? $_GET['articleId'] : 0;
$idUser = isset($_SESSION['idUser']) ? $_SESSION['idUser'] : '';

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

$spShowTopics = DBSP_PShowTopics;



$topicsList = <<< EOD

		<table>
		<tr class='topicsListHead'>
 			<th class='topicsL' style='width:210px; color:#333333;'>Ämne</th>
 			<th class='topics' style='width:50px; color:#333333;'>Poster</th>
 			<th class='topics' style='width:120px; color:#333333;'>Skriven av</th>
 			<th class='topics' style='width:170px; color:#333333;'>Datum</th>
 		</tr>

EOD;
		
		$query = "call {$spShowTopics}();";
		$mysqli = $db->connectToDatabase();
		$res = $db->performDirectQuery($query);
		
		
		
		//------------------------------ retrieving and performing query

		
		while($row = $res->fetch_object()) {
			
			$articleId = $row->idTopic;
			$articleTitle = $row->title;
			$nrOfPosts = $row->posts;
			$author = $row->username;
			$articleDate = $row->created;
			
			
			
			$topicsList .= <<< EOD
				<tr class='topicsList' onclick="document.location.href='?m=tuna&amp;p=showtopic&amp;articleId={$articleId}';" onmouseout="this.style.background='#ffffff';" onmouseover="this.style.background='#deedf9';this.style.cursor='pointer'">
					<td class='topicsL'><p class='topicsListTitle'>{$articleTitle}</p></td>
					<td class='topics'><p class='topicsListText'>{$nrOfPosts}</p></td>
					<td class='topics'><p class='topicsListText'>{$author}</p></td>
					<td class='topics'><p class='topicsListText'>{$articleDate}</p></td>
				</tr>
EOD;
			
		}
		
$topicsList .= <<< EOD
				<tr>
					<td class='topics'></td>
					<td class='topics'></td>
					<td class='topics'></td>
					<td class='topics'></td>
				</tr>
	</table>
EOD;

//---------------------------------------------------------------------------------------------
//
//    the content of the page
//



$centerBody = <<< EOD
	<div class='articleFrame'>
		{$topicsList}
	</div>
EOD;
$leftBody = "";
$rightBody = <<< EOD
	

EOD;
$subHeader = <<< EOD
	<h1>Senaste diskussionerna</h1>
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