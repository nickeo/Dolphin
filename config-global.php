<?php
// ===========================================================================================
//
//
// 	Description: Global site specific configurations. Same for all modules and pagecontrollers.
// 
// 	Author: Niklas Odén
//

//----------------------------------------------------------------------------------------------
//
//	structuring the site
//

define('TP_SOURCEPATH', dirname(__FILE__) . "/src/");
define('TP_PAGESPATH', dirname(__FILE__) . "/modules/core/");
define('TP_STYLEPATH', dirname(__FILE__) . '/stylesheets/');
define('TP_SQLPATH', dirname(__FILE__) . '/sql/');
define('TP_EXAMINPATH', dirname(__FILE__) . '/modules/kmoms/');
define('TP_MODULEPATH', dirname(__FILE__) . '/modules/');
define('TP_TUNATALKPATH', dirname(__FILE__) . '/modules/tunatalk/');
define('TP_JAVAPATH', dirname(__FILE__) . '/js/');


// -------------------------------------------------------------------------------------------
//
// Settings for commonly used external resources, for example javascripts.
//
define('JS_JQUERY', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js');        


// -------------------------------------------------------------------------------------------
//
// These modules (TP_MODULEPATH) are available.
//
$gModulesAvailable = Array(
        'core'    => TP_MODULEPATH . 'core',			// The core, always included
        'tuna' => TP_MODULEPATH . 'tunatalk',		// TunaTalk - the Dolphin forum
        'examin'    => TP_MODULEPATH . 'kmoms',		// Academic reports, examinations
        'files'		=> TP_MODULEPATH . 'filearchive'	// handling archive and file upload/download
    );

//----------------------------------------------------------------------------------------------
//
//	constants controlling special features
//

define('USE_RECAPTCHA', true);
define('USER_SELF_REGISTER', true);
define('USER_GRAVATAR', true);
define('SESSION_TIME', 900);

?>