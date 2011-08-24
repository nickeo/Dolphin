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
*    Description: installing information
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

//---------------------------------------------------------------------------------------------
//
//    taking care of post and get variables
//

global $gModule;

$action = "?m={$gModule}&amp;p=installp";

$showErrorMessage = "";
$loginRedirect = "";
$redir = "";

$errorMessage = isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : '';
unset($_SESSION['errorMessage']);

if($errorMessage != '') {

    $showErrorMessage = <<< EOD
        <div class='errorMessage'>
            {$errorMessage}
        </div>
EOD;

}



//---------------------------------------------------------------------------------------------
//
//    the content of the page
//

$htmlHead = "";
$js = <<< EOD
	<script type="text/javascript">
	
  		$(document).ready(function() {
  			
				$('#form1').click(function(event) {
					if ($(event.target).is('#custom')) {
						uncheckInstall();				
					} else if ($(event.target).is('#account')) {
						uncheckCustom();				
					} else if ($(event.target).is('#files')) {
						uncheckCustom();
					} else if ($(event.target).is('#article')) {
						uncheckCustom();
					} else if ($(event.target).is('#tunatalk')) {
						uncheckCustom();
					} 
				});
				
				$('a.changeVisibility').click(function(event) {
					if ($(event.target).is('#customLink')) {
						$('#showCustom').toggle();
						return false;
					} else if ($(event.target).is('#accountLink')) {
						$('#showAccount').toggle();
						return false;				
					} else if ($(event.target).is('#filesLink')) {
						$('#showFiles').toggle();
						return false;				
					} else if ($(event.target).is('#articlesLink')) {
						$('#showArticles').toggle();
						return false;				
					} else if ($(event.target).is('#tunatalkLink')) {
						$('#showTunatalk').toggle();
						return false;				
					}
				});
 			
 				function uncheckInstall() {
 					if($('#custom').attr('checked') == true) {
 						$('#account').attr('checked', false);
 						$('#files').attr('checked', false);
 						$('#article').attr('checked', false);
 						$('#tunatalk').attr('checked', false);
 					}
 				}
 			
 				function uncheckCustom() {
 					$('#custom').attr('checked', false);
 				}
           
					
		});
  			
  			
  	</script>
EOD;


$subHeader = "<div class='adRightTurquoise'><a href='?m=core&amp;p=admincontrol' class='big'>Kontrollpanel</a></div><h1>Installation av databas</h1>";


$centerBody = <<< EOD
   	
    <form action='{$action}' method='post' id='form1'>
    	 
    	 <div class='settingsWrapperUser'>
    	 	
    	 	<hr class='soft'>
    	 	<div class='settingsLeft'>
    	 		<input type="checkbox" name="custom" value="custom" id="custom" checked='checked'/>		
    	 	</div>
    	 	<div class='settingsInstall'>
    	 		<div class='right'><a href='' class='changeVisibility' id='customLink' >Visa/dölj detaljer</a></div>
    	 		<p class='settings'>Standardinstallation</p>
    	 	</div>
    	 	
    	 	<div class='clear'></div>
    	 	<div id='showCustom' style='display:none;'>
    	 		<p style='padding:10px;'>Installerar tabeller och procedurer för nedanstående fyra delar: användare, filhantering, artiklar och forumet Tunatalk.
    	 		För mer info ser respektive del.</p>
    	 	</div>
    	 </div>
    	 
    	 <div class='settingsWrapperUser'>
    	 	
    	 	<hr class='soft'/>
    	 	<div class='settingsLeft'>
    	 		<input type="checkbox" name="account" id="account" value="account" />
    	 	</div>
    	 	<div class='settingsInstall'>
    	 	<div class='right'><a href='' class='changeVisibility' id='accountLink' >Visa/dölj detaljer</a></div>
    	 		<p class='settings'>Användare</p>
    	 	</div>
    	 	
    	 	<div class='clear'></div>
    	 	<div id='showAccount' style='display:none;'>
    	 		<pre>
+ tables
	user - information about users
	group - information about different groups available in dolphin
	groupmember - connecting user with group/rights
	
+ functions
	CheckUserIsAdmin - check if current user is admin
	CreatePassword - generating password => hash(pwd + salt)
	GetGravatarLink - creating link using user email
	
+ stored procedures
	CreateAccount -	
	AuthenticateUser -
	GetAccountInfo -
	UpdatePassword -
	UpdateEmail -
	UpdateAvatar -
	UpdateGravatar -
			
+ Default values
	populating user and grouptables with one default admin user
    	 		</pre>
    	 	</div>
    	 </div>
    	 <div class='settingsWrapperUser'>
    	
    	 	<hr class='soft'>
    	 	<div class='settingsLeft'>
    	 		<input type="checkbox" name="files" id="files" value="files" />
    	 		
    	 	</div>
    	 	<div class='settingsInstall'>
    	 		<div class='right'><a href='' class='changeVisibility' id='filesLink' >Visa/dölj detaljer</a></div>
    	 		<p class='settings'>Filhantering</p>
    	 	</div>
    	 	
    	 	<div class='clear'></div>
    	 	<div id='showFiles' style='display:none;'>
    	 		<pre>
+ tables
	files - information about files
	
+ functions
	CheckFilePermission -
	
+ stored procedures
	InsertFile -
	ListFiles -
	GetFileDetails -
	UpdateOrDeleteFile -
	ListTrash -
	AdminListFiles -
	AdminListAccounts -
    	 		</pre>
    	 	</div>
    	 </div>
    	 <div class='settingsWrapperUser'>
    	
    	 	<hr class='soft'>
    	 	<div class='settingsLeft'>
    	 		<input type="checkbox" name="article" id="article" value="article" />
    	 		
    	 	</div>
    	 	<div class='settingsInstall'>
    	 		<div class='right'><a href='' class='changeVisibility' id='articlesLink' >Visa/dölj detaljer</a></div>
    	 		<p class='settings'>Artiklar</p>
    	 	</div>
    	 	
    	 	<div class='clear'></div>
    	 	<div id='showArticles' style='display:none;'>
    	 		<pre>
+ tables
	articles - info about articles, ie title, text, date ...

+ functions
	CheckIfUserIsAdminOrOwner -
	GetGroup -
	
+ stored procedures
	CreateNewArticle -
	UpdateArticle -
	DisplayArticle -
	ListArticles -
	
    	 		</pre>
    	 	</div>
    	 </div>
    	  <div class='settingsWrapperUser'>
    	 	
    	 	<hr class='soft'>
    	 	<div class='settingsLeft'>
    	 		<input type="checkbox" name="tunatalk" id="tunatalk" value="tunatalk" />	
    	 	</div>
    	 	<div class='settingsInstall'>
    	 		<div class='right'><a href='' class='changeVisibility' id='tunatalkLink' >Visa/dölj detaljer</a></div>
    	 		<p class='settings'>Tunatalk</p>
    	 	</div>
    	 	
    	 	<div class='clear'></div>
    	 	<div id='showTunatalk' style='display:none;'>
    	 		<pre>
+ tables
	topic -	stores actual topic ie title and text
	information - stores information about the topic
	attachment - connecting files and topics
	
+ stored procedures
	CreateOrUpdateTopic -
	DisplayTopic -
	ShowTopics -
	DeletePost -
	DisplayPosts -
	DisplayTopicAndPosts -
	GetTopic -
	GetAttachment -
	GetTopicAndAttachment -
	AttachFile -
	
+ triggers
	RemoveAttachment -
    	 		</pre>
    	 	</div>
    	 </div>
    	  
         <div class='settingsWrapperUser'>
         	<hr class='soft' />
         </div>
        <div class='clear'></div>
        <button type='submit' value='submit' class='buttonBig' >Installera</button>
	</form>
    	
    
EOD;

$leftBody = "";

$rightBody = <<< EOD
		
EOD;
//echo $body;
	

//---------------------------------------------------------------------------------------------
//
//	printing out the page
//
$title = "Installera";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody, $htmlHead, $js);

?>