<?php
//------------------------------------------------------------------
//
//	controlling layout
//
//------------------------------------------------------------------


$style = "style.css";


$_SESSION['officialOrPersonal'] = isset($_SESSION['officialOrPersonal']) ? $_SESSION['officialOrPersonal'] : 'off';
$_SESSION['pageStyle'] = isset($_GET['layout']) ? $_GET['layout'] : '';

if($_SESSION['pageStyle'] != '') {
	
	$_SESSION['officialOrPersonal'] = $_SESSION['pageStyle'] == 'per' ? 'off' : 'per';
	
} 

if($_SESSION['officialOrPersonal'] == 'per') {
	$layout = 'stylesheets/style.css';
} else {
	$layout = 'stylesheets/style2.css';
}

?>