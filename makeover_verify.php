<?php
$error_text = "";

if(isset($_POST['address'])){
	if($_POST['own_home'] <> 'yes'){
		$error_text .= "You must own this home to qualify.<br />";
	}; // endif
	
	if($_POST['single_family'] <> 'yes'){
		$error_text .= "This residence a single family residence to qualify.<br />";
	}; // endif
	
	if($_POST['primary_residence'] <> 'yes'){
		$error_text .= "This residence must be your primary residence to qualify.<br />";
	} // endif
	
	if($_POST['in_oregon'] <> 'yes'){
		$error_text .= "Your residence must be in Oregon to qualify.<br />";
	} // endif
	
	if(!(($_POST['heat'] == 'gas') or ($_POST['heat'] == 'other' ) or ($_POST['heat'] == 'electric'))){
		$error_text .= "Illegal heat type.  You must have electricity or gas as your primary heating source.<br />";
	} // endif
	
	if($_POST['heat'] == 'other' ){
		$error_text .= "You must have electricity or gas as your primary heating source.<br />";
	} // endif
	
	if(!(($_POST['utility_gas'] == 'other') or ($_POST['utility_gas'] == 'NWN') or ($_POST['utility_gas'] == 'CNG')) and ($_POST['heat'] == 'gas')){ // if heat is gas and not (cascade, NWN, or other)
		$error_text .= "You must select an gas utility if you choose gas as your primary heat source.<br />";
	} // endif
	
	if(!(($_POST['utility_electric'] == 'other') or ($_POST['utility_electric'] == 'PP') or ($_POST['utility_electric'] == 'PGE')) and ($_POST['heat'] == 'electric')){ // if heat is electric and not (PAC, PGE, or other)
		$error_text .= "You must select an electric utility if you choose electricity as your primary heat source.<br />";
	} // endif
	
	if( !(isset($_POST['FirstName'])) or  $_POST['FirstName'] == ''){ 
		$error_text .= "You must enter both your first name.<br/>";
	} // endif

	if( !(isset($_POST['LastName'])) or  $_POST['LastName'] == ''){ 
		$error_text .= "You must enter both your last name.<br/>";
	} // endif

	if ($error_text <> '') {
		echo json_encode(array("ErrorText" 	=> $error_text)); // end array
		die;
	} // endif $error_text not empty
	
	require_once 'securimage/securimage.php';
  	$img = new Securimage;
	if ($img->check($_POST['code']) == false) {
		$Captcha_Status = "false";
		echo json_encode(array("CaptchaStatus" 	=> $Captcha_Status)); // end array
		die;
	}else{
		$Captcha_Status = "true";
	} // endif captcha fails
	
	// normalize address
	$eto_soap = new SoapClient("https://forms.energytrust.org/AddrProvider.asmx?WSDL");
	
	$params = array('streetaddress1' => $_POST['address'],
					'city'           => $_POST['city'],
					'state'          => $_POST['state'],
					'zipCode'	     => $_POST['ZipCode']
					); // end array $params
	
	try{
		$result = $eto_soap->getBarCodeAndProviders($params);
	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "<br />";
		echo $eto_soap->__getLastRequest ();
	}
	
	$Street		= $result->getBarCodeAndProvidersResult->outStreet;
	$City 		= $result->getBarCodeAndProvidersResult->outCity;
	$State		= $result->getBarCodeAndProvidersResult->outState;
	$Zip 		= $result->getBarCodeAndProvidersResult->outZip;
	$Plus4 		= $result->getBarCodeAndProvidersResult->outPlus4;
	$Barcode 	= $result->getBarCodeAndProvidersResult->outBarCode;
	$ErrorCode 	= $result->getBarCodeAndProvidersResult->ErrorCode;
	$EUtility 	= $result->getBarCodeAndProvidersResult->custUtilities->elecUtility;
	$GUtility 	= $result->getBarCodeAndProvidersResult->custUtilities->gasUtility;
	$ErrorExtMsg= $result->getBarCodeAndProvidersResult->ErrorExtMsg;
	
	if(isset($ErrorExtMsg)){
		$ErrorText = $ErrorExtMsg;
	}else{
		$ErrorText = "";
	}
	
	$json_return = json_encode(array("Street" 	=> $Street,
									 "City"		=> $City,
									 "State"	=> $State,
									 "Zip"		=> $Zip,
									 "Plus4"	=> $Plus4,
									 "Barcode"	=> $Barcode,
									 "ErrorCode"=> $ErrorCode,
									 "Electric_Utility" => $EUtility,
									 "Gas_Utility"		=> $GUtility,
									 "Provider" => $Provider,
									 "Captcha_Status" => $Captcha_Status,
									 "ErrorText" => $ErrorText
									 ) // end array
								);
	echo $json_return;
} // endif isset($_POST['address'])
?>
 