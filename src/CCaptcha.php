<?php
// ===========================================================================================
//
// Description: Class CCaptcha
//
//


class CCaptcha {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	public $iErrorMessage;


	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct() {
		$this->iErrorMessage = "";
	}


	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() { ; }


	// ------------------------------------------------------------------------------------
	//
	// Get HTML to display the CAPTCHA
	//
	public function GetRecaptchaHTML() {
		$this->iErrorMessage = "";

		require_once(TP_SOURCEPATH . '/recaptcha/recaptchalib.php');
		$publickey = RECAPTCHA_PUBLIC; // you got this from the signup page
		return recaptcha_get_html($publickey);
	}


	// ------------------------------------------------------------------------------------
	//
	// Validate the answer
	//
	public function CheckAnswer() {
		$this->iErrorMessage = "";

		require_once(TP_SOURCEPATH . '/recaptcha/recaptchalib.php');
		$privatekey = RECAPTCHA_PRIVATE;
		$resp = recaptcha_check_answer ($privatekey,
		$_SERVER["REMOTE_ADDR"],
		$_POST["recaptcha_challenge_field"],
		$_POST["recaptcha_response_field"]);

		if (!$resp->is_valid) {
		$this->iErrorMessage = "The reCAPTCHA wasn't entered correctly. Go back and try it again." .
		"(reCAPTCHA said: " . $resp->error . ")";
		return FALSE;
	}
		return TRUE;
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// customize recaptcha
	//
	
	public function CustomizeCaptcha($aStyle) {
		
		
		$customizeCaptcha = <<< EOD
			<script type="text/javascript">
 				var RecaptchaOptions = {
    			theme : '$aStyle'
 				};
 			</script>
EOD;

		return $customizeCaptcha;
	}

} // End of Of Class


?>