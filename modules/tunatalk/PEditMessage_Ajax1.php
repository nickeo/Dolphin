<?php
/*********************************************************************************************
*
*    Description: form for new message
*
*    Author: Niklas Odén
*
***********************************************************************************************/

$pc = new CPageController();

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
//	taking care of variables
//

$articleId = isset($_GET['articleId']) ? $_GET['articleId'] : 0;
$postId = isset($_GET['postId']) ? $_GET['postId'] : 0;
$tblTopic = DBT_Topic;
$topicId = ($postId != 0) ? $postId : $articleId;
$parentId = ($postId == 0) ? $postId : $articleId;
// db -table
$tblTopic = DBT_Topic;

//---------------------------------------------------------------------------------------------
//
//	quering database
//

$query = "SELECT * FROM {$tblTopic} WHERE idTopic = {$topicId};";

$db = new CDatabaseController();
$mysqli = $db->connectToDatabase();
$res = $db->performDirectQuery($query);

$row = $res->fetch_object();

$title = $row->topicTitle;
$content = $row->topicText;

$res->close();
$mysqli->close();

if(isset($_GET['articleTitle'])) {

	
	$articleTitle = isset($_GET['articleTitle']) ? $_GET['articleTitle'] : '';

	
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

//---------------------------------------------------------------------------------------------
//
//   creating necessary objects
//

$artNav = new CNavigation();
$articleNav = $artNav->CreateLinksToArticles();
$pc = new CPageController();

//---------------------------------------------------------------------------------------------
//
//    the content of the page
//


$deleteLink = <<< EOD
	<a href='?m=tuna&amp;p=deletep&amp;postId={$postId}&amp;articleId={$articleId}'style='margin: 0 20px 0 0;' class='comment'>Ta bort post/ämne</a>
EOD;
$deleteLink = ($iFilter->UserIsAdmin()) ? $deleteLink : '';

$javaPath = WS_JAVASCRIPT;

$htmlHead = <<< EOD
	<link rel='stylesheet' type='text/css' href='{$javaPath}jgrowl/jquery.jgrowl.css' />
	<script type='text/javascript' src='{$javaPath}jgrowl/jquery.jgrowl.js'></script>
	<script type='text/javascript' src='{$javaPath}jquery-autosave/jquery.form.js'></script>
	<script type='text/javascript' src='{$javaPath}jquery-autosave/jquery.autosave.js'></script>
EOD;

$js = <<< EOD
	<script type="text/javascript">
	
	
		function showRequest(formData, options) { 
   					
   					 $('#dold').val("Saved");
   					 var queryString = $.param(formData);
    				$.jGrowl('About to submit: ' + queryString); 
    				
				} 
 

				function showResponse(responseText, statusText)  {
					$('#infoDiv').show();
    				alert('status: ' + statusText + 'responseText: ' + responseText);
    				$('#infoDiv').fadeOut(2500);
				} 
	
  		$(document).ready(function() {
  		
  		var options = {
					
						target:  '#infoDiv',
						// dataType: 'json',
						beforeSubmit: showRequest,
						success: showResponse
	
					};
					
				$('#form1').ajaxForm(options);
  		
		$.jGrowl("Det här är Growl. En informationshjälp.");
	$('fieldset.formatField').click(function(event) {
  				if ($(event.target).is('button#publish')) {
  					$('#form1').submit();
  					return false;
  				} else if ($(event.target).is('button#save')) {
  					$.jGrowl("Texten sparad");
  					$('input#jsRedirect').val('true');
  					$('input#jsRedirectUrl').val('?m=tuna&p=edittopic&articleId={$topicId}');
  					$('#form1').submit();
  					return false;
  				} else if ($(event.target).is('button#cancel')) {
  					history.back();
  				}
  				
  			});
  			
  		});
  	</script>
EOD;

$centerBody = <<< EOD
 
    <fieldset class='formatField' id='article'>
    <legend class='formatlegend'></legend>
        <div style='float:left;'>
        <form id='form1' action='?m=tuna&amp;p=newtopicp&amp;articleId={$topicId}' method='POST'>
        <input type='hidden' name='parentId' value='{$parentId}'>
        <input type='hidden' name='jsRedirect' id='jsRedirect' value='false'>
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
    </fieldset>
EOD;

$leftBody = "";
$rightBody = <<< EOD
	{$articleNav}
	
EOD;
$subHeader = <<< EOD
	<h1>Redigera post</h1>
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