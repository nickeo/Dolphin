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
*    Description: presenting upload form
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
$iFilter->UserLoginStatus();
//$iFilter->UserGroupStatus();

//---------------------------------------------------------------------------------------------
//
//	creating objects
//
$pc = new CPageController();
$nav = new CNavigation();

//---------------------------------------------------------------------------------------------
//
//	taking care of variables
//
$navSet = $pc->GetIsSetOrSetDefault('p');
$maxFileSize = MAX_FILE_SIZE;

// path to javascript
$javaPath = WS_JAVASCRIPT;

$imageLink = WS_IMAGES;

//---------------------------------------------------------------------------------------------
//
//	script
//

$htmlHead = <<< EOD
	<link rel='stylesheet' type='text/css' href='{$javaPath}jgrowl/jquery.jgrowl.css' />
	<script type='text/javascript' src='{$javaPath}jgrowl/jquery.jgrowl.js'></script>
	<script type='text/javascript' src='{$javaPath}jquery-autosave/jquery.form.js'></script>
	<script type='text/javascript' src='{$javaPath}jquery-autosave/jquery.autosave.js'></script>
EOD;

$js = <<< EOD
	<script type="text/javascript">
	
  		$(document).ready(function() {
  		
  			$.jGrowl("Sida för uppladdning!");
  			
  			// Preload loader image
    var loader = new Image();
    loader.src = "{$imageLink}ajax-loader.gif";
    loader.align = "baseline";
				
				$('#form1').ajaxForm({
        // $.ajax options can be used here too, for example: 
        //timeout: 1000, 

        // return a datatype of json
        //dataType: 'json',
        
        // remove short delay before posting form when uploading files
        //forceSync: true,
        
        // form should always target the server response to an iframe. This is useful in conjuction with file uploads.
        //iframe: true,
        
        // do stuff before submitting form
        beforeSubmit: function(data, status) {
                        $.jGrowl('Before submit...');
                        $('#ajaxsubmit').val('ajaxupload');
                        $('#uploadStatus').html(loader);
                        //$('#debug1').html('');
                },
                
        // define a callback function
        success: function(data, status) {
                        $.jGrowl("Uploaded file. Done.");
                        $('#uploadStatus').html(data);
                        //$('#debug1').html(print_r(data, true));
                }    
        });
				
				
			});
				
					
					
  		
  	</script>
EOD;



//---------------------------------------------------------------------------------------------
//
//    the content of the page
//

$subHeader = "<h1>Ladda upp filer</h1>";
$navigation = $nav->userControlNavigation($navSet);

//$test = strtolower($_SESSION['accountUser']) . mktime();

$centerBody = <<< EOD
    
        <div class='formatLogin'>
        <form enctype="multipart/form-data" action='?m=core&amp;p=uploadp' method='post' id='form1'>
        <input type='hidden' name="MAX_FILE_SIZE" value='$maxFileSize' />
            <h3>Ladda upp en fil - Ajax style</h3>
            <table>
            <tr>
            <td class='login'>Välj fil:</td>
            <td class='login'><input type='file' name='file' size='30'/></td>
            </tr>
            <tr>
            <td colspan='2' class='button'><button type='submit' class='submitbutton' id='ajaxsubmit' value='ajaxupload' name='dosubmit'>Ladda upp</button></td>
            </tr>
            </table>
    		<span id='uploadStatus'></span>
        </form>
        
        <hr style='border-style:solid; border-width:1px 0 0 0; border-color:#dddddd; margin: 20px 0 20px 0;' />
        <form enctype="multipart/form-data" action='?m=core&amp;p=uploadp' method='post'>
        <input type='hidden' name="MAX_FILE_SIZE" value='$maxFileSize' />
            <h3>Ladda upp en fil</h3>
            <table>
            <tr>
            <td class='login'>Välj fil:</td>
            <td class='login'><input type='file' name='file' size='30'/></td>
            </tr>
            <tr>
            <td colspan='2' class='button'><button type='submit' class='submitbutton' value='singlefile' name='dosubmit' onclick="this.value='singlefile';">Ladda upp</button></td>
            </tr>
            </table>
    		
        </form>
       		<hr style='border-style:solid; border-width:1px 0 0 0; border-color:#dddddd; margin: 20px 0 20px 0;' />
       	<form enctype="multipart/form-data" action='?m=core&amp;p=uploadp' method='post'>
        <input type='hidden' name="MAX_FILE_SIZE" value='$maxFileSize' />
            <h3>Ladda upp flera filer</h3>
            <table>
            <tr>
            <td class='login'>Välj filer:</td>
            <td class='login'><input type='file' name='file[]' size='30'/></td>
            </tr>
            <tr>
            	<td style='border-width:0px;'></td>
            	<td class='login'><input type='file' name='file[]' size='30'/></td>
            </tr>
             <tr>
            	<td style='border-width:0px;'></td>
            	<td class='login'><input type='file' name='file[]' size='30'/></td>
            </tr>
            <tr>
            <td colspan='2' class='button'><button type='submit' class='submitbutton' value='multiplefiles' name='dosubmit' onclick="this.value='multiplefiles';">Ladda upp</button></td>
            </tr>
            </table>
    		
        </form>
       	
        </div>
        
        <div class='clear'></div>
    
    <br />
    <br />
EOD;

$leftBody = <<< EOD
EOD;

$rightBody = <<< EOD
	{$navigation}
EOD;

	

//---------------------------------------------------------------------------------------------
//
//	printing out the page
//
$title = "Skapa nytt konto";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody, $htmlHead, $js);

?>