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
//	interceptionfilter
//
//

if(!$frontDoor) 
	die('No direct access to pagecontroller allowed!');

$iFilter = new CInterceptionFilter();
$iFilter->FrontControllerIsVisited();
$iFilter->UserLoginStatus();

//---------------------------------------------------------------------------------------------
//
//    creating objects
//

$pc = new CPageController();

//---------------------------------------------------------------------------------------------
//
//	connecting required files
//

require_once(TP_SQLPATH . 'config.php');
require_once(TP_SQLPATH . 'CSQL.php');


//---------------------------------------------------------------------------------------------
//
//	taking care of variables and constants
//

$articleId = isset($_GET['articleId']) ? $_GET['articleId'] : 0;
$postId = isset($_GET['postId']) ? $_GET['postId'] : 0;
$newtopic = isset($_GET['p']) ? $_GET['p'] : 'none';
$tblTopic = DBT_Topic;
$topicId = ($postId != 0) ? $postId : $articleId;
$parentId = ($postId == 0) ? $postId : $articleId;

// db -table and stored procedure
$tblTopic = DBT_Topic;
$spGetTopicAndAttachment = DBSP_PGetTopicAndAttachment;
// path to javascript
$javaPath = WS_JAVASCRIPT;

$attachedFile = "";
//---------------------------------------------------------------------------------------------
//
//   creating necessary objects
//

$artNav = new CNavigation();
$articleNav = $artNav->CreateLinksToArticles();
$pc = new CPageController();


//---------------------------------------------------------------------------------------------
//
//	preparing and performing query
//


//checking if quering is needed
if($newtopic == 'newtopic') {
	$parentId = $articleId;
	$articleId = 0;
	$topicId = 0;
}

/*if($articleId == 0) {
	$parentId = $articleId;
	$articleId = 0;
	$topicId = 0;
}*/


if($articleId != 0) {
	$query = "call {$spGetTopicAndAttachment}({$topicId});";
	//$query = "SELECT * FROM {$tblTopic} WHERE idTopic = {$topicId};";
	$db = new CDatabaseController();
	$mysqli = $db->connectToDatabase();
	//$res = $db->performDirectQuery($query);
	$result = $db->performMultiQueryAndStore($query);
	$row = $result[0]->fetch_object();
	//$row = $res->fetch_object();
	$title = $row->topicTitle;
	$content = $row->topicText;
	$headline = "Redigera post";
	$result[0]->close();
	
	if($result[1]->num_rows > 0) {
	$attachedFile = <<< EOD
	<h4>Bifogade filer:</h4>
EOD;
	while($row = $result[1]->fetch_object())
	{
		$name = $row->name;
		$uniqueName = $row->uniqueName;
		$path = $row->path;
		$userId = $row->userId;
		$file = $row->fileId;
		
		$linkUrl = "?m=files&amp;p=attachfilep&amp;file=" . $file . "&amp;article=" . $topicId . "&amp;delete=true";
		
		$attachedFile .= <<< EOD
			<a href='{$linkUrl}' title='Ta bort bifogad fil' >{$name}</a>
EOD;

	}
	
	}
	$result[1]->close();
	$mysqli->close();

} else {

	$title = "";
	$content = "";
	$headline = "Ny artikel";
}

//---------------------------------------------------------------------------------------------
//
//    change form depending on usage - new, post or topic
//

if(isset($_GET['articleTitle'])) {

	$articleTitle = $_GET['articleTitle'];
	$title= "Aktuellt ämne: \"" . $articleTitle ."\"";
	$theTitle = <<< EOD
			<td style='border:0px; padding: 0px 0px 20px 0px;'><h3>{$title}</h3></td>
            <td style='border:0px;'></td>
EOD;

} else {
	
	$theTitle = <<< EOD
			<td style='border:0px; padding: 0px 0px 20px 0px;'><label for='articleTitle'>Titel</label></td>
            <td style='border:0px;'><input type='text' name='articleTitle' value='{$title}' size=50; class='articleForm'/></td>
EOD;
}

// adding delete-functionality for admin/current user
$deleteLink = <<< EOD
	<a href='?m=tuna&amp;p=deletep&amp;postId={$postId}&amp;articleId={$articleId}'style='margin: 0 20px 0 0;' class='comment'>Ta bort post/ämne</a>
EOD;
$deleteLink = ($iFilter->UserIsAdmin()) ? $deleteLink : '';


//---------------------------------------------------------------------------------------------
//
//    the content of the page
//


$htmlHead = <<< EOD
	<link rel='stylesheet' type='text/css' href='{$javaPath}jgrowl/jquery.jgrowl.css' />
	<script type='text/javascript' src='{$javaPath}jgrowl/jquery.jgrowl.js'></script>
	<script type='text/javascript' src='{$javaPath}jquery-autosave/jquery.form.js'></script>
	<script type='text/javascript' src='{$javaPath}jquery-autosave/jquery.autosave.js'></script>
EOD;

$successRedirect = 'json';
$js = <<< EOD
	<script type="text/javascript">
	
  		$(document).ready(function() {
  		
  		$(".markItUp").markItUp(mySettings);
  		
  		var autosave = {
					
						time: 5000,
					
						id: null,
					
						performAutosave: function() {
							$('#form1').submit();
							$('button#save').attr('disabled', 'disabled');
							$.jGrowl('Texten sparad!');
						},
						
						performPublish: function() {
							$('#form1').submit();
							$('button#save').attr('disabled', 'disabled');
							$.jGrowl('Artikeln sparad och publicerad!');
						
						},
					
						detectKeypress: function(event) {
							$('#form1').unbind('keypress');
							autosave.id = setTimeout(autosave.performAutosave, autosave.time);
							$('button#save').removeAttr('disabled');
							$('button#publish').removeAttr('disabled');
							$('input#saveOrPublish').val('save');
							$.jGrowl('Texten ändrad!');
						},
					
						beforeSaving: function(formData) {
							clearTimeout(autosave.id);
							var queryString = $.param(formData);
							$('#form1').bind('keypress', autosave.detectKeypress);
						},
						
						showResponse: function(data, status) {
							$.jGrowl('Status: ' + status + ' <br />articleId: ' + data.articleId + '<br />parentId: ' + data.parentId + '<br />Tid: ' + data.timestamp);
							$('#articleId').val(data.articleId);
							$('#parentId').val(data.parentId);
						}
				
					};
					
					var options = {
					
						target:  '#infoDiv',
						dataType: 'json',
						beforeSubmit: function(formData) {
								autosave.beforeSaving(formData);
							},
						success: function(data, status) {
							autosave.showResponse(data, status);
						}
					
					};
					
					
				$('#form1').bind('keypress', autosave.detectKeypress);
					
					$('#form1').ajaxForm(options);
				
					$('#form1').click(function(event) {
					if ($(event.target).is('button#publish')) {
					
						$('input#saveOrPublish').val('publish');
						$('button#save').attr('disabled', 'disabled');
						$('button#publish').attr('disabled', 'disabled');
						autosave.performPublish();
						$('#form1').unbind('keypress');
						return false;
										
					} else if ($(event.target).is('button#save')) {
						$('input#saveOrPublish').val('save');
						$('button#save').attr('disabled', 'disabled');
						autosave.performAutosave();
						$('#form1').unbind('keypress');
						return false;
					} else if ($(event.target).is('button#cancel')) {
					//alert('discard');
					history.back();
					}
					
					});
					
					$('#attach').submit(function() {
			
				var	form2 = $('#attach'),  
       			 		formUrl = form2.attr('action'),  
        				formMethod = form2.attr('method'),  
        				responseMessage = $('#targetDiv');
        				
        			$.ajax({  
            			url: formUrl,  
            			type: formMethod,
            			dataType: 'html',
            			success:function(data){
            				$('#targetDiv').empty();
                			$('#targetDiv').append(data);
                			
                		}
                		
                })
				
				return false;
			})
			
				$('#delete').submit(function() {
			
				var	form2delete = $('#delete'),  
       			 		formUrl = form2delete.attr('action'),  
        				formMethod = form2delete.attr('method'),  
        				responseMessage = $('#targetDiv');
        				
        			$.ajax({  
            			url: formUrl,  
            			type: formMethod,
            			dataType: 'html',
            			success:function(data){
            				$('#targetDiv').empty();
                			$('#targetDiv').append(data);
                			
                		}
                		
                })
				
				return false;
			})
			
			$('#file_form').live('submit', function() {
				
				var	form3 = $('#file_form'),  
     					formData = form3.serialize(),
       			 		formUrl = form3.attr('action'),  
        				formMethod = form3.attr('method'), 
        				responseMessage = $('#attachmentDiv');
        				
        			$.ajax({  
            			url: formUrl,  
            			type: formMethod,
            			data: formData,
            			dataType: 'html',
            			success:function(data){
            				$('#attachmentDiv').empty();
                			$('#attachmentDiv').append(data);
                			$('#targetDiv').empty();
                			
                		}
                		
                })
                
            return false;
			})
			
					
		});
  			
  			
  	</script>
EOD;


$centerBody = <<< EOD
 
    
        <div style='float:left;'>
        <form id='form1' action='?m=tuna&amp;p=newtopicp&amp;articleId={$topicId}' method='POST'>
        <input type='hidden' id='parentId' name='parentId' value='{$parentId}'>
        <input type='hidden' id='articleId' name='articleId' value='{$topicId}'>
        <input type='hidden' name='saveOrPublish' id='saveOrPublish' value=''>
        <input type='hidden' name='jsRedirect' id='jsRedirect' value='{$successRedirect}'>
        <input type='hidden' name='jsRedirectUrl' id='jsRedirectUrl' value='?m=tuna&amp;p=edittopic&amp;articleId={$topicId}'>
            <br/><br/>
            <table>
            <tr>
            {$theTitle}
            </tr>
            <tr>
            
            <td style='border:0px; padding: 0 0 10px 0;'colspan='2'><textarea  name='articleText'  rows='22' cols='65' class="markItUp">{$content}</textarea></td>
            </tr>
            <tr>
            <td colspan='2' style='border:0px; text-align:right;'>{$deleteLink}
            <button type='submit' class='publishbutton' name='publish' id='publish' >Publicera</button>
            <button type='submit' class='savebutton' name='save' id='save'>Spara</button>
            <button type='reset' class='cancelbutton' name='cancel' id='cancel'>Ångra</button></td>
            </tr>
            </table>
    <div id='infoDiv'></div>
        </form>
        </div>
        <div class='right'>
        	
        </div>
        <div class='clear'></div>
    
EOD;

$leftBody = "";

if($postId > 0) {
	$rightBody = <<< EOD
		{$articleNav}
EOD;
} else {
$rightBody = <<< EOD
	{$articleNav}
	<br />
		<form id="attach" name="attach" action="?m=core&amp;p=attachfile&amp;idart={$topicId}" method="post" style='display:inline;'>
			<button type="submit" value='submit' id='submitfile'>Bifoga fil</button>
		</form>
		<form id="delete" name="delete" action="?m=core&amp;p=attachfile&amp;idart={$topicId}&amp;delete=true" method="post" style='display:inline;'>
			<button type="submit" value='delete' id='deletefile' name='deletefile'>Ta bort fil</button>
		</form>
	<div id='attachmentDiv'>
		<br />
		{$attachedFile}
	</div>
	<div id='targetDiv'>
	
	</div>
EOD;
}
$subHeader = <<< EOD
	<h1>{$headline}</h1>
EOD;


//---------------------------------------------------------------------------------------------
//
//	printing out the page
//
$title = "Ny artikel";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody, $htmlHead, $js);

?>