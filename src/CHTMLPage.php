<?php
/*********************************************************************************************
*
*	Description: printing the htmlpage
*
*	Author: Niklas Odén
*
***********************************************************************************************/

class CHTMLPage {

	//-----------------------------------------------------------------------------------------
	//
	//	member variables
	//
	
	protected $cssLink;
	protected $iLink;
	
	//------------------------------------------------------------------------------------------
	//
	//	constructor and destructor
	//
	
	function __construct($aCssLink = WS_STYLESHEET) {
		$this->cssLink = $aCssLink;
		
	}
	
	function __destruct() {
		;
	}
	
	//------------------------------------------------------------------------------------------
	//
	//	printing out the page
	//
	
	public function PrintPage($aTitle = "", $aSubHeader = "", $aleftBody = "", $acenterBody = "", $arightBody = "", $aHtmlHead="", $aJS = "") {
	
		$favicon = WS_FAVICON;
		$language = WS_LANGUAGE;
		$charset = WS_CHARSET;
		$wsTitle = WS_TITLE;
		$title = (!empty($aTitle) ? ($wsTitle . " - " . $aTitle) : $wsTitle);
		$javaPath = WS_JAVASCRIPT;
		$javascript = (empty($aJS)) ? '' : $aJS;
		
		
		$htmlHeader = $this->HTMLHeader($title, $language, $charset, $favicon, $aHtmlHead, $javaPath, $javascript);
		$pageHeader = $this->PageHeader();
		$pageBody = $this->PageBody($aSubHeader, $aleftBody, $acenterBody, $arightBody);
		$pageFooter = $this->PageFooter();
		
		
		
		$printPage = $htmlHeader . $pageHeader . $pageBody . $pageFooter;
		
		echo $printPage;
	
	}
	
	
	//------------------------------------------------------------------------------------------
	//
	//	controling HTML-header
	//
	
	public function HTMLHeader($aTitle, $language, $charset, $favicon, $aHtmlHead, $javaPath, $javascript) {
	
		$htmlHeader = <<< EOD
			
<!DOCTYPE html>
<html lang="{$language}">
				<head>
				<meta charset="{$charset}" />
					<title>{$aTitle}</title>
					
					<script type="text/javascript" src="{$javaPath}latest/jquery.js"></script>
					<script type="text/javascript" src="{$javaPath}latest/markitup/jquery.markitup.js"></script>
					<script type="text/javascript" src="{$javaPath}latest/markitup/sets/html/set.js"></script>
					{$aHtmlHead}
					{$javascript}
					
					<link rel="stylesheet" type="text/css" href="http://www.student.bth.se/~niod09/dbwebb2/kmom3/dolphin/js/latest/markitup/skins/markitup/style.css" />
					<link rel="stylesheet" type="text/css" href="http://www.student.bth.se/~niod09/dbwebb2/kmom3/dolphin/js/latest/markitup/sets/html/style.css" />
					
					<link rel="shortcut icon" href="{$favicon}" />
					<link rel='stylesheet' type='text/css' href='{$this->cssLink}' />

				</head>
EOD;
		return $htmlHeader;
	
	}
	
	//------------------------------------------------------------------------------------------
	//
	//	login-logout
	//
	
	public function GetLoginLogout() {
		
		
		if(USER_SELF_REGISTER == true) {
			$register = <<< EOD
				<div class='right'>
					<a href='?m=core&amp;p=createa' class='log'>Register</a>
				</div>
EOD;
		} else { $register = ""; }
		
		if(isset($_SESSION['accountUser'])) {
			
			
			$loghtml = <<< EOD
			
			<div class='right'>
				<a href='?p=logout' class='log'>Logout</a>
			</div>
			<div class='right'>
				<a href='?m=core&amp;p=usercontrol' class='log'>{$_SESSION['accountUser']}</a>
			</div>
EOD;
			if(isset($_SESSION['groupMemberUser']) && $_SESSION['groupMemberUser'] == "adm") {
				$loghtml .= <<< EOD
					<div class='right'>
						<a href='?m=core&amp;p=admincontrol' class='log'>admin</a>
					</div>
EOD;
			}
			
		} else {
		
		$loghtml = <<<EOD
		<div class='right'>
			<a href='?p=login' class='log'>Login</a>
		</div>
		{$register}
EOD;
		
		}
		return $loghtml;
	}
	
	//------------------------------------------------------------------------------------------
	//
	//	controling page header
	//
	
	public function PageHeader($aHeader = WS_TITLE) {
	
		$link = "";
		$i = 1;
		global $gModule;
		
		$logoLink = ($gModule == "core") ? "?m=tuna&amp;p=tunatalk" : "?m=core&amp;p=home";
		$logoTitle = ($gModule == "core") ? "TunaTalk" : "Dolphin - startsida";
		
		$loginlogout = $this->GetLoginLogout();
		$navBar = $this->PrepareNavigationBar();
		
		
	$pageHeader = <<< EOD
	<body>
				<div id='bodyWrapper'>
					<div id='headerLine'>
							{$loginlogout}
					</div>
					<div id='headerWrapper'>
						<div id='headerNav'>
							{$navBar}
							<div class='clear'></div>
						</div>
					</div>
					<div id='headerIcon'>
						<a href='{$logoLink}' title='{$logoTitle}'><img src='images/dolphin1s.png' alt='Delfin' /></a>
					</div>
				
EOD;
	
		return $pageHeader;
	}
	
	//------------------------------------------------------------------------------------------
	//
	//	controling page body
	//
	
	public function PageBody($aSubHeader, $aleftBody, $acenterBody, $arightBody) {
	
		$pageBody = <<<EOD
			<div id='mainWrapper'>
				<div id='subHeader'>
					{$aSubHeader}
				</div>
				<div class='leftColumn'>
					{$aleftBody}
				</div>
				<div class='rightColumn'>
					{$arightBody}
				</div>
				<div class='centerColumn'>
					{$acenterBody}
				</div>
				<div class='clear'></div>
			</div>
			
			
			
EOD;
		
		
		return $pageBody;
	}
	
	
	//------------------------------------------------------------------------------------------
	//
	//	controling page footer
	//
	
	public function PageFooter($aFooter = WS_FOOTER) {
		
		$pageFooter = <<< EOD
			
			<div id = 'footerWrapper'>
				{$aFooter}<br />
				<a href='http://html5.validator.nu/' class='footerNav'>HTML5</a>
				<a href='http://validator.w3.org/check?uri=referer' class='footerNav'>&nbsp;&nbsp;XHTML</a>
				<a href='http://jigsaw.w3.org/css-validator/' class='footerNav'>&nbsp;CSS</a>
			</div>
			</div>
			</body>
		</html>
EOD;
		return $pageFooter;
	}
	
	// ------------------------------------------------------------------------------------
    //
    // Prepare the header-div of the page
    //
    public function PrepareNavigationBar() {
    
        global $gModule;
        
        $menu = unserialize(WS_MENY);
        
        $nav = "<div id='headerLeft'></div>";
        foreach($menu as $key => $value) {
            $nav .= "<div class='left'><a href='{$value}' class='hNav'>{$key}</a></div>";
        }
        
        if(isset($_SESSION['accountUser']) && $gModule =='core') {
        	$nav .= "<div class='left'><a href='?p=showmessage&amp;newArticle=1' class='hNav'>Artiklar</a></div>";
        
        }
        
        
        
    
        return $nav;    
    }

}

?>