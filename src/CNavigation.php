<?php
/*********************************************************************************************
*
*	Description: class handling navigation
*
*	Author: Niklas Odén
*
***********************************************************************************************/
	
	require_once(TP_SQLPATH . 'CSQL.php');
	require_once(TP_SQLPATH . 'config.php');
	
	
class CNavigation {

	//-----------------------------------------------------------------------------------------
	//
	//	member variables
	//
	

	
	//-----------------------------------------------------------------------------------------
	//
	//	constructor and destructor
	//
	
	function __construct() {
	
		
	}
	
	function __destruct() {
	
	}
	
	//-----------------------------------------------------------------------------------------
	//
	//	creating vertical navigation used when handling articles
	//
	
	public function articleNavigation($articleId = "", $aEditor = "") {
			
			if(!empty($articleId) && $aEditor == TRUE) {
				$editLink = "<a href='?p=editmessage&amp;articleId={$articleId}' class='articleNav'>Editera artikel</a>";
			} else {
				$editLink = "<a href='' class='articleNavForbidden' title='Ej editerbar' >Editera artikel</a>";
			}
			
		$articleNav = <<< EOD
			<h3>Aktiviteter</h3>
			<hr class='artNav' />
			<a href='?p=newmessage' class='articleNav'>Skriv ny artikel</a>
			<br />
			{$editLink}
			<br />
			<a href='?p=listarticles&amp;articleList=cur' class='articleNav'>Mina artiklar</a>
			<br />
			<a href='?p=listarticles&amp;articleList=all' class='articleNav'>Alla artiklar</a>
			<br />
			<br />
			<h3>Senaste artiklar</h3>
			<hr class='artNav' />
EOD;
		
		//----------------------------- connecting to database
		$db = new CDatabaseController();
		$mysqli = $db->connectToDatabase();
		
		//------------------------------ retrieving and performing query
		$queryObject = new CSQL();
		$query = $queryObject->getLatestArticles();
		$res = $db->performDirectQuery($query);
		
		while($row = $res->fetch_object()) {
			
			$articleId = $row->id;
			$articleTitle = $row->articleTitle;
			$articleNav .= <<< EOD
				<a href='?p=showmessage&amp;articleId={$articleId}' class='articleNav'>{$articleTitle}</a>
				<br />
EOD;
		}
		
		return $articleNav;
	
	}
	
	//---------------------------------------------------------------------------------------------
	//
	//   creating links to the latest articles
	//
	
	public function CreateLinksToArticles() {
	
		$spShowTopics = DBSP_PShowTopics;
		$db = new CDatabaseController();

	$topicsList = <<< EOD

		<h3>Senaste artiklar</h3>
			<hr class='artNav' />

EOD;
		
		$query = "call {$spShowTopics}();";
		$mysqli = $db->connectToDatabase();
		$res = $db->performDirectQuery($query);
		
		
		
		//------------------------------ retrieving and performing query

		
		while($row = $res->fetch_object()) {
			
			$articleId = $row->idTopic;
			$articleTitle = $row->title;
			
			
			
			
			$topicsList .= <<< EOD
				<a href='?m=tuna&amp;p=showtopic&amp;articleId={$articleId}' class='articleNav'>{$articleTitle}</a>
				<br />
EOD;
			
		}
		
		return $topicsList;
	}
	
	//----------------------------------------------------------------------------------------------
	//
	//	creating navigation for User Control Panel
	//
	
	public function userControlNavigation($aPage) {
		
		$link = "?m=core&amp;p=";
		$showLink = Array();
		
		for($i=0;$i<4;$i++) {
			$showLink[$i] = "articleNav";
		}
		
		switch($aPage) {
			case 'usercontrol' : $showLink[0] = "noLink"; break;
			case 'accounts' : $showLink[1] = "noLink"; break;
			case 'archive' : $showLink[2] = "noLink"; break;
			case 'details' : $showLink[2] = "noLink"; break;
			case 'download' : $showLink[2] = "noLink"; break;
			case 'editfile' : $showLink[2] = "noLink"; break;
			case 'upload' : $showLink[3] = "noLink"; break;
			default : ; break;
		}
		
		$navigation = <<< EOD
			<h3>Kontrollpanel</h3>
			<hr class='soft'/>
			<a href='{$link}usercontrol' title='' class='{$showLink[0]}' >Användaruppgifter</a><br />
			<a href='{$link}accounts' title='' class='{$showLink[1]}'>Ändra dina uppgifter</a><br />
			<a href='{$link}archive' title='' class='{$showLink[2]}'>Filarkiv - se/editera/ta bort</a><br/>
			<a href='{$link}upload' title='' class='{$showLink[3]}'>Ladda upp filer</a><br />
			
EOD;

		return $navigation;

	}
	
	//----------------------------------------------------------------------------------------------
	//
	//	creating sub-navigation
	//
	
	public function SubNavigation($aHeadLine, $aNavArray) {
		
		$navigation = <<< EOD
			<h3>{$aHeadLine}</h3>
			<hr class='soft'/>
EOD;
		
		foreach($aNavArray as $key => $value) {
            $navigation .= "<a href='{$value}' class='articleNav'>{$key}</a><br />";
        }
			
			
		return $navigation;
	}
	
	} // end of class
?>