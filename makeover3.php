<?php require_once('Connections/makeover.php'); ?>
<?php

$debug = false; 	  // set to false for production
$sendXML = false; // set to true for production

// get data form electrical provider dropdown
$electricProviderSQL = "SELECT * FROM  `electric_providers` ORDER BY  `name`"; 
mysql_select_db($database_makeover, $makeover_connection);
$electricProviders = mysql_query($electricProviderSQL, $makeover_connection) or die(mysql_error());
$row_electricProviders = mysql_fetch_assoc($electricProviders);
$totalRows_electricProviders = mysql_num_rows($electricProviders);

// get data for gas provider dropdown
$gasProviderSQL = "SELECT * FROM  `gas_providers` ORDER BY  `name`"; 
mysql_select_db($database_makeover, $makeover_connection);
$gasProviders = mysql_query($gasProviderSQL, $makeover_connection) or die(mysql_error());
$row_gasProviders = mysql_fetch_assoc($gasProviders);
$totalRows_gasProviders = mysql_num_rows($gasProviders);

// Process the form when it is submitted.
if(isset($_POST['Submit'])){
    
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

} // endif Get_Barcode_And_Provider

if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
	{
	  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
	
	  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
	
	  switch ($theType) {
		case "text":
		  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
		  break;    
		case "long":
		case "int":
		  $theValue = ($theValue != "") ? intval($theValue) : "NULL";
		  break;
		case "double":
		  $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
		  break;
		case "date":
		  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
		  break;
		case "defined":
		  $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
		  break;
	  }
	  return $theValue;
	}
}

if(isset($_POST['enter_contest'])){
	// load database
	
	// defend against unset utilities
	if(!isset($_POST['utility_gas'])){$_POST['utility_gas'] = 'none';}
	if(!isset($_POST['utility_electric'])){$_POST['utility_electric'] = 'none';}
	
	mysql_select_db($database_makeover, $makeover_connection);
	$insertRequestSQL = sprintf("INSERT into `requests`(
								`created_at`,
								`own_home`,
								`single_family`,
								`primary_residence`,
								`in_oregon`,
								`heat`,
								`utility_gas`,
								`utility_electric`,
								`gas_account_number`,
								`electric_account_number`,
								`FirstName`,
								`LastName`,
								`address`,
								`address2`,
								`city`,
								`state`,
								`ZipCode`,
								`phone`,
								`email`,
								`yearBuilt`,
								`floors`,
								`squareFeet`
								)VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
								'NOW()',
								GetSQLValueString($_POST['own_home'], "text"),
								GetSQLValueString($_POST['single_family'], "text"),
								GetSQLValueString($_POST['primary_residence'], "text"),
								GetSQLValueString($_POST['in_oregon'], "text"),
								GetSQLValueString($_POST['heat'], "text"),
								GetSQLValueString($_POST['utility_gas'], "text"),
								GetSQLValueString($_POST['utility_electric'], "text"),
								GetSQLValueString($_POST['gas_account_number'], "text"),
								GetSQLValueString($_POST['electric_account_number'], "text"),
								GetSQLValueString($_POST['FirstName'], "text"),
								GetSQLValueString($_POST['LastName'], "text"),
								GetSQLValueString($_POST['address'], "text"),
								GetSQLValueString($_POST['address2'], "text"),
								GetSQLValueString($_POST['city'], "text"),
								GetSQLValueString($_POST['state'], "text"),
								GetSQLValueString($_POST['ZipCode'], "text"),
								GetSQLValueString($_POST['phone'], "text"),
								GetSQLValueString($_POST['email'], "text"),
								GetSQLValueString($_POST['yearBuilt'], "text"),
								0,
								GetSQLValueString($_POST['squareFeet'], "int")
								);
	if($debug){echo "InsertRequestSQL -> $insertRequestSQL<br />";}
	$requests = mysql_query($insertRequestSQL, $makeover_connection) or die(mysql_error());
	$row_requests = mysql_fetch_assoc($requests);
	$totalRows_requests = mysql_num_rows($requests);
	
	$getRequestIDSQL = "SELECT  LAST_INSERT_ID() as id";
	$idResult = mysql_query($getRequestIDSQL, $makeover_connection) or die(mysql_error());
	$row_id =  mysql_fetch_assoc($idResult);
	$request_id = $row_id['id'];
	if($debug){echo "InsertedRequestID -> $request_id, gas_month1 -> {$_POST['gas_month1']}<br />";}
	
	insertReading($request_id, 'gas', date("c", strtotime($_POST['gas_month1'])), $_POST['gas_reading1']);
	insertReading($request_id, 'gas', date("c", strtotime($_POST['gas_month2'])), $_POST['gas_reading2']);
	insertReading($request_id, 'gas', date("c", strtotime($_POST['gas_month3'])), $_POST['gas_reading3']);
	insertReading($request_id, 'gas', date("c", strtotime($_POST['gas_month4'])), $_POST['gas_reading4']);
	insertReading($request_id, 'gas', date("c", strtotime($_POST['gas_month5'])), $_POST['gas_reading5']);
	insertReading($request_id, 'gas', date("c", strtotime($_POST['gas_month6'])), $_POST['gas_reading6']);
	insertReading($request_id, 'gas', date("c", strtotime($_POST['gas_month7'])), $_POST['gas_reading7']);
	insertReading($request_id, 'gas', date("c", strtotime($_POST['gas_month8'])), $_POST['gas_reading8']);
	insertReading($request_id, 'gas', date("c", strtotime($_POST['gas_month9'])), $_POST['gas_reading9']);
	insertReading($request_id, 'gas', date("c", strtotime($_POST['gas_month10'])), $_POST['gas_reading10']);
	insertReading($request_id, 'gas', date("c", strtotime($_POST['gas_month11'])), $_POST['gas_reading11']);
	insertReading($request_id, 'gas', date("c", strtotime($_POST['gas_month12'])), $_POST['gas_reading12']);
	
	insertReading($request_id, 'ele', date("c", strtotime($_POST['ele_month1'])), $_POST['ele_reading1']);
	insertReading($request_id, 'ele', date("c", strtotime($_POST['ele_month2'])), $_POST['ele_reading2']);
	insertReading($request_id, 'ele', date("c", strtotime($_POST['ele_month3'])), $_POST['ele_reading3']);
	insertReading($request_id, 'ele', date("c", strtotime($_POST['ele_month4'])), $_POST['ele_reading4']);
	insertReading($request_id, 'ele', date("c", strtotime($_POST['ele_month5'])), $_POST['ele_reading5']);
	insertReading($request_id, 'ele', date("c", strtotime($_POST['ele_month6'])), $_POST['ele_reading6']);
	insertReading($request_id, 'ele', date("c", strtotime($_POST['ele_month7'])), $_POST['ele_reading7']);
	insertReading($request_id, 'ele', date("c", strtotime($_POST['ele_month8'])), $_POST['ele_reading8']);
	insertReading($request_id, 'ele', date("c", strtotime($_POST['ele_month9'])), $_POST['ele_reading9']);
	insertReading($request_id, 'ele', date("c", strtotime($_POST['ele_month10'])), $_POST['ele_reading10']);
	insertReading($request_id, 'ele', date("c", strtotime($_POST['ele_month11'])), $_POST['ele_reading11']);
	insertReading($request_id, 'ele', date("c", strtotime($_POST['ele_month12'])), $_POST['ele_reading12']);
	
	
} // endif isset($_POST['Done'])
//  Month init
if (!isset($_POST['gas_month1'])){$_POST['gas_month1'] = date("m/d/Y", strtotime("-12 month"));}
if (!isset($_POST['gas_month2'])){$_POST['gas_month2'] = date("m/d/Y", strtotime("-11 month"));}
if (!isset($_POST['gas_month3'])){$_POST['gas_month3'] = date("m/d/Y", strtotime("-10 month"));}
if (!isset($_POST['gas_month4'])){$_POST['gas_month4'] = date("m/d/Y", strtotime("-9 month"));}
if (!isset($_POST['gas_month5'])){$_POST['gas_month5'] = date("m/d/Y", strtotime("-8 month"));}
if (!isset($_POST['gas_month6'])){$_POST['gas_month6'] = date("m/d/Y", strtotime("-7 month"));}
if (!isset($_POST['gas_month7'])){$_POST['gas_month7'] = date("m/d/Y", strtotime("-6 month"));}
if (!isset($_POST['gas_month8'])){$_POST['gas_month8'] = date("m/d/Y", strtotime("-5 month"));}
if (!isset($_POST['gas_month9'])){$_POST['gas_month9'] = date("m/d/Y", strtotime("-4 month"));}
if (!isset($_POST['gas_month10'])){$_POST['gas_month10'] = date("m/d/Y", strtotime("-3 month"));}
if (!isset($_POST['gas_month11'])){$_POST['gas_month11'] = date("m/d/Y", strtotime("-2 month"));}
if (!isset($_POST['gas_month12'])){$_POST['gas_month12'] = date("m/d/Y", strtotime("-1 month"));}

if (!isset($_POST['ele_month1'])){$_POST['ele_month1'] = date("m/d/Y", strtotime("-12 month"));}
if (!isset($_POST['ele_month2'])){$_POST['ele_month2'] = date("m/d/Y", strtotime("-11 month"));}
if (!isset($_POST['ele_month3'])){$_POST['ele_month3'] = date("m/d/Y", strtotime("-10 month"));}
if (!isset($_POST['ele_month4'])){$_POST['ele_month4'] = date("m/d/Y", strtotime("-9 month"));}
if (!isset($_POST['ele_month5'])){$_POST['ele_month5'] = date("m/d/Y", strtotime("-8 month"));}
if (!isset($_POST['ele_month6'])){$_POST['ele_month6'] = date("m/d/Y", strtotime("-7 month"));}
if (!isset($_POST['ele_month7'])){$_POST['ele_month7'] = date("m/d/Y", strtotime("-6 month"));}
if (!isset($_POST['ele_month8'])){$_POST['ele_month8'] = date("m/d/Y", strtotime("-5 month"));}
if (!isset($_POST['ele_month9'])){$_POST['ele_month9'] = date("m/d/Y", strtotime("-4 month"));}
if (!isset($_POST['ele_month10'])){$_POST['ele_month10'] = date("m/d/Y", strtotime("-3 month"));}
if (!isset($_POST['ele_month11'])){$_POST['ele_month11'] = date("m/d/Y", strtotime("-2 month"));}
if (!isset($_POST['ele_month12'])){$_POST['ele_month12'] = date("m/d/Y", strtotime("-1 month"));}

function insertReading($request_id, $type, $date, $reading){

	global $makeover_connection;
	global $database_makeover;

	if($request_id == 0 or !isset($request_id)){return false;} // check parameters
	if($type == ''      or !isset($type)      ){return false;}
	if($date == ''      or !isset($date)      ){return false;}
	if($reading == 0    or !isset($reading)   ){return false;}
	
	mysql_select_db($database_makeover, $makeover_connection);
	$insertReadingSQL =  sprintf("INSERT into `readings`(`request_id`,`type`,`date`,`reading`)VALUES(%s,%s,%s,%s)",
								GetSQLValueString($request_id, "int"),
								GetSQLValueString($type, "text"),
								GetSQLValueString($date, "date"),
								GetSQLValueString($reading, "int"));
	$insertReadingResult = mysql_query($insertReadingSQL, $makeover_connection) or die(mysql_error());
	$row =  mysql_fetch_assoc($insertReadingResult);
	$totalRows_Readings = mysql_num_rows($insertReadingResult);
	
	if($totalRows_Readings > 0){
		return true;
	}else{
		return false;
	}
	
} // end function insertReading()
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Win a Home Energy Makeover</title>
<link href="../../Styles/makeover.css" rel="stylesheet" type="text/css" />
<link href="./css/datePicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/JS/jquery.js"></script>
<script type="text/javascript" src="/JS/jquery.datePicker.js"></script>
<script type="text/javascript" src="/JS/jquery.bgifram.js"></script>
<script type="text/javascript" src="/JS/date.js"></script>
<script type="text/javascript">
     $(document).ready(function() {
	
		Date.firstDayOfWeek = 7;
		Date.format = 'mm/dd/yyyy';
		
		$('.date-pick').datePicker({
			startDate: '01/01/2008',
			endDate: (new Date()).asString()
		});
        
        // Let's see if this works!
        
        $("#thankyou").hide(); // hide thank you page
        
        $("#disagree").hide(); // hide disagree page
        
        if('<?php echo $_POST['enter_contest'];?>' != ''){
            // DEBUG alert('I Agree has been posted.');
			$("#thankyou").show(); // hide thank you page
            $("#mainContent").hide(); // hide thank you page
		}; 
        
        $("#introHeader").show();
        
        $("#hear").show();
        
        $("#oregon").hide(); // end #oregon, initially hide
        
		$("#oregon_np").hide(); // end #oregon_np, initially hide
        
        $("#oregon_np2").hide(); // end #oregon_np2, initially hide
        
        $("#own").hide(); // end #own, initially hide
        
	   	$("#own_nq").hide(); // end #own_nq, initially hide
		
		$("#singleFamily").hide(); // end #singleFamily, initially hide
		
		$("#mf_nq").hide(); // end #mf_nq, initially hide
		 
		$("#primary").hide(); // end #primary, initially hide
		
		$("#heat").hide(); // end #heat, iniially hide
		
		$("#heat_nq").hide(); // end #heat_nq, initially hide
		
		$("#utility_gas").hide(); // end #utility_gas, initially hide
		
		$("#heat_np").hide(); // end #heat_np, initially hide
		
		$("#utility_electric").hide(); // end #utility_electric, initially hide
		
		$("#utility_gas").hide(); // end #utility_gas, initially hide		

		$("#heat_nq").hide(); // end #heat_nq, initially hide		

		$("#form").hide(); // end #heat_nq, initially hide	
		
		$("#gas_readings").hide();  // initally hide #gas_readings
        
        $("#enter_gas").hide();  // initially hide submit button
        
        $("#show_disclaimer").hide(); // initially hide the continue button
        
        $("#disclaimer").hide(); // initially hide the disclaimer
        
        $("#enter_contest").hide(); // hide the enter the contest button

		// disable gas_month text boxes, only editable through date selectors
		$("input[type=text]").filter("[id^='gas_month']").each(function() {
			$(this).attr("disabled", true);
		}); // end each
		
		// disable ele_month text boxes, only editable through date selectors
		$("input[type=text]").filter("[id^='ele_month']").each(function() {
			$(this).attr("disabled", true);
		}); // end each
		
        // Submit the Address form
		$("form").submit(function(){ // reenable on submit
                
				$("input[type=text]").filter("[id^='gas_month']").each(function() {
					$(this).attr("disabled", false);
				}); // end each
				
				// disable ele_month text boxes, only editable through date selectors
				$("input[type=text]").filter("[id^='ele_month']").each(function() {
					$(this).attr("disabled", false);
				}); // end each
		}); // end form submission

        // How did you hear about us?
        $("#hear").click(hear_selected);
        function hear_selected(){
            $("#hear").hide("slow"); // hide current question
            $("#introHeader").hide(); // hide the intro header text
            $("#oregon").show("slow"); // show the next question
        };

        // Is your home in Oregon?
        $("#button_oregon_yes").click(button_oregon_yes);		
		if('<?php echo $_POST['in_oregon'];?>' == 'yes'){
			button_oregon_yes();
		}; // endif
		
		function button_oregon_yes() {
            $("#own").show("slow"); // show "own" question
            $("#oregon").hide("slow"); // hide current question
			$("#oregon_np").hide("slow"); // hide "own_nq" answer
			$("#button_oregon_yes").addClass("green");
			$("#button_oregon_no").removeClass("red");
			$("#in_oregon").val("yes");
   		}; // end #button_primary_yes

		$("#button_oregon_no").click(button_oregon_no);
		if('<?php echo $_POST['in_oregon'];?>' == 'no'){
			button_oregon_no();
		}; // endif
		
		function button_oregon_no() {
     		$("#oregon_np").show("slow"); // show own_nq answer
			$("#heat").hide("slow"); // hide heat question
			$("#button_oregon_no").addClass("red");
			$("#button_oregon_yes").removeClass("green");
			$("#in_oregon").val("no");
   		}; // end #button_primary_no
        // end Is your home in Oregon?
        

        // Do you own your home?
        // Yes!
   		$("#button_own_yes").click(button_own_yes);
		if('<?php echo $_POST['own_home'];?>' == 'yes'){
			button_own_yes();
		};
		// No!
		$("#button_own_no").click(button_own_no);
		if('<?php echo $_POST['own_home'];?>' == 'no'){
			button_own_no();
		};
		
		function button_own_yes() {
     		$("#singleFamily").show("slow"); // show singleFamily question
            $("#own").hide("slow"); // hide current question
			$("#own_nq").hide("slow"); // hide own_nq answer
			$("#button_own_yes").addClass("green");
			$("#button_own_no").removeClass("red");
			$("#own_home").val("yes");
   		}; // end function button_own_yes()
		
		function button_own_no() {
     		$("#own_nq").show("slow"); // show own_nq answer
			$("#singleFamily").hide("slow"); // hide singleFamily question
			$("#button_own_no").addClass("red");
			$("#button_own_yes").removeClass("green");
			$("#own_home").val("no");
   		}; // end #button_own_no
        // end Do you own your home?

        


        // Is your home a single-family home?
		if('<?php echo $_POST['single_family'];?>' == 'yes'){
			button_mf_yes();
		}; // endif

		$("#button_mf_yes").click(button_mf_yes);
		
		function button_mf_yes() {
     		$("#primary").show("slow"); // show own_nq answer
            $("#singleFamily").hide("slow"); // hide last question
			$("#mf_nq").hide("slow"); // hide own_nq answer
			$("#button_mf_yes").addClass("green");	
 			$("#button_mf_no").removeClass("red");	
			$("#single_family").val("yes");
  		}; // end #button_mf_no
		
		if('<?php echo $_POST['single_family'];?>' == 'no'){
			button_mf_no();
		}; // endif

		$("#button_mf_no").click(button_mf_no);
		
		function button_mf_no() {
     		$("#mf_nq").show("slow"); // show own_nq answer
     		$("#primary").hide("slow"); // hide own_nq answer
			$("#button_mf_no").addClass("red");
			$("#button_mf_yes").removeClass("green");
			$("#single_family").val("no");
   		}; // end #button_mf_no
        // end Is your home a single-family home?

    
        // Is your single-family home your primary residence?
        $("#button_primary_yes").click(button_primary_yes);
		if('<?php echo $_POST['primary_residence'];?>' == 'yes'){
			button_primary_yes();
		}; // endif
		
		function button_primary_yes() {
     		$("#heat").show("slow"); // show heat question
            $("#primary").hide("slow"); // hide last question
			$("#oregon_np2").hide("slow"); // hide own_nq answer
			$("#button_primary_yes").addClass("green");
			$("#button_primary_no").removeClass("red");
			$("#primary_residence").val("yes");
   		}; // end #button_primary_yes

		$("#button_primary_no").click(button_primary_no);
		if('<?php echo $_POST['primary_residence'];?>' == 'no'){
			button_primary_no();
		}; // endif
		
		function button_primary_no() {
     		$("#oregon_np2").show("slow"); // show own_nq answer
			$("#oregon").hide("slow"); // show oregon question
			$("#button_primary_no").addClass("red");
			$("#button_primary_yes").removeClass("green");
			$("#primary_residence").val("no");
   		}; // end #button_primary_no
        // end Is your single-family home your primary residence?


        // What is your primary heating fuel?
		$("#heat_electric").click(heat_electric);
		
		if('<?php echo $_POST['heat'];?>' == 'electric'){
			heat_electric();
		}; // endif
		
		function heat_electric() {
     		$("#utility_electric").show("slow"); // show #utility_electric question
			$("#utility_gas").hide("slow"); // hide #utility_gas question
			$("#heat_nq").hide("slow"); // hide #heat_nq statement
            $("#heat_np2").hide("slow"); // hide #heat_np2 statement
   		}; // end #heat_electric
		
		$("#heat_gas").click(heat_gas);
		
		if('<?php echo $_POST['heat'];?>' == 'gas'){
			heat_gas();
		}; // endif
		
		function heat_gas() {
     		$("#utility_electric").hide("slow"); // hide #utility_electric question
			$("#utility_gas").show("slow"); // show #utility_gas question
			$("#heat_nq").hide("slow"); // hide #heat_nq statement			
   		}; // end #heat_electric
		
		$("#heat_other").click(heat_other);

		if('<?php echo $_POST['heat'];?>' == 'other'){
			heat_other();
		}; // endif
		
		function heat_other() {
     		$("#utility_electric").hide("slow"); // hide #utility_electric question
			$("#utility_gas").hide("slow"); // hide #utility_gas question
			$("#heat_nq").show("slow"); // show #heat_nq statement			
   		}; // end #heat_electric
		
		$("#utility_gas_cascade").click(utility_gas_cascade);
		
		if('<?php echo $_POST['utility_gas'];?>' == 'cascade'){
			utility_gas_cascade();
		}; // endif
		
		function utility_gas_cascade() {
			$("#form").show("slow"); // show #form
			$("#heat_np").hide("slow"); // hide #heat_np statement
            $("#meter_readings").hide("slow"); // hide the meter form for now
   		}; // end #heat_electric
		
		$("#utility_gas_nwn").click(utility_gas_nwn);
		
		if('<?php echo $_POST['utility_gas'];?>' == 'NWN'){
			utility_gas_nwn();
		}; // endif

		function utility_gas_nwn() {
			$("#form").show("slow"); // show #form		
			$("#heat_np").hide("slow"); // hide #heat_np statement
            $("#meter_readings").hide("slow"); // hide the meter form for now
   		}; // end #heat_electric
		
		$("#utility_gas_other").click(utility_gas_other);
		
		if('<?php echo $_POST['utility_gas'];?>' == 'other'){
			utility_gas_other();
		}; // endif
		
		function utility_gas_other() {
			$("#form").hide("slow"); // hide #form		
			$("#heat_np").show("slow"); // show #heat_np statement
   		}; // end #heat_electric
		
		$("#utility_electric_pge").click(utility_electric_pge);
		
		if('<?php echo $_POST['utility_electric'];?>' == 'PGE'){
			utility_electric_pge();
		}; // endif
		
		function utility_electric_pge() {
            $("#heat_np2").hide("slow"); // hide #heat_np statement
			$("#form").show("slow"); // show #form
            $("#meter_readings").hide("slow"); // hide the meter form for now
   		}; // end #heat_electric
		
		$("#utility_electric_pac").click(utility_electric_pac);
	
		if('<?php echo $_POST['utility_electric'];?>' == 'PAC'){
			utility_electric_pac();
		}; // endif
		
		function utility_electric_pac() {
            
			$("#heat_np2").hide("slow"); // hide #heat_np statement
			$("#form").show("slow"); // show #form
            $("#meter_readings").hide("slow"); // hide the meter form for now
   		}; // end #heat_electric
		
		$("#utility_electric_other").click(utility_electric_other);

		if('<?php echo $_POST['utility_electric'];?>' == 'other'){
			utility_electric_other();
		}; // endif
		
		function utility_electric_other() {
			$("#heat_np2").show("slow"); // show #heat_np statement
			$("#form").hide("slow"); // hide #form
   		}; // end #heat_electric
		
        
        // Disclaimer form
        $("#show_disclaimer").click(show_disclaimer);
        function show_disclaimer(){
            $("#meter_readings").hide("slow"); // hide the form
            $("#disclaimer").show("slow");
            $("#enter_contest").show(); // hide the enter the contest button
        }
        
        // Show I'm Sorry page
        $("#not_enter_contest").click(show_sorrypage);
        function show_sorrypage(){
            $("#disclaimer").hide("slow"); // hide the disclaimer page
            $("#disagree").show("slow"); // show the i'm sorry page
        }
        
//////////
        // The Verify button is responsible for form processing and
        // web service integration. -ALU
//////////
		$("#verify").click(function() {
			$("#Error_Text").html(""); // erase errors
			var form_vals = $("form").serialize(); // grab variables from form
			
            $.post("makeover_verify2.php", form_vals, function(data){ // ajax post data
                //DEBUG alert("Captcha Status -> " + data.Captcha_Status + ", ErrorCode:" + data.ErrorCode + ", Error Text: " + data.ErrorText);

                // The security code check was successful.
                if(data.Captcha_Status == 'true'){
					$("#results").hide();
					$("#results").html("Barcode:" + data.Barcode + 
										", Electric Utility:" + data.Electric_Utility +
										", Gas Utility: " + data.Gas_Utility);
					$("#results").show("slow");
					$("#meter_readings").hide("slow"); // to being, hide "Enter Meter Readings
	
					if(data.ErrorCode == '0'){ // if we verified Ok
						// Set normalized address in form
						$("#address").val(data.Street);
						$("#city").val(data.City);
						$("#state").val(data.State);
						$("#ZipCode").val(data.Zip + "-" + data.Plus4);
					};
                    //DEBUG alert("Heat -> " + $("input[name='heat']:checked").val());

                    // 1. If both utilities are null, show both forms side-by-side.
                    // 2. If Gas is null, show the gas form.
                    // 3. If Elec is null, show the elec form.
                    // 4. If neither are null, show the next form.
                    //
                    
                    // No utilities were found, show both forms.
                    if ((data.Gas_Utility == null) && (data.Electric_Utility == null)) {
                        //DEBUG alert('No utilities were detected.');
                        $("#meter_readings").show("slow"); // show "Enter Meter Readings"
                        $("#electric_readings_table").show("slow"); // show electric
						$("#gas_readings_table").show("slow"); // show gas
                        
                    // No electric utility found, show just that form.
                    } else if ((data.Electric_Utility == null) && (data.Gas_Utility != null)) {
                        //DEBUG alert('No electric utility was detected.');
                        $("#formtable").hide("slow"); // hide the form
                        $("#meter_readings").show("slow"); // show "Enter Meter Readings"
						$("#gas_readings_table").hide(); // hide gas
                        $("#electric_readings_table").show("slow"); // show electric
                        
                    // No gas utility found, show just that form.
                    } else if ((data.Electric_Utility != null) && (data.Gas_Utility == null)) {
                        //DEBUG alert('No gas utility was detected.');
                        $("#formtable").hide("slow"); // hide the form
                        $("#meter_readings").show("slow"); // show "Enter Meter Readings"
						$("#gas_readings_table").show("slow"); // show gas
                        $("#electric_readings_table").hide(); // hide electric
                        
                    // All good, move on.
                    } else {
                        //DEBUG alert('You are good, next form!');
                        $("#disclaimer").show("slow");
                        $("#enter_contest").show();
                    }
                    
                    //DEBUG alert("Show the other stuff now!");
                    
                    $("#formtable").hide("slow"); // hide the form
                    $("#heat").hide(); // hide the primary heating question
                    $("#utility_gas").hide(); // hide the utility gas question
                    $("#utility_electric").hide(); // hide the utility electric question
                    $("#show_disclaimer").show("slow"); // show the continue button

				}else{
                    if(data.ErrorText != ''){ // We have errors, lets handle them.
                        
                        // This error is raised if the user selects the wrong utility company but IS found in the address database.
                        if(data.ErrorText.indexOf('You must select an electric utility') == 0){
                            alert("Our records show that your Electric Company is XX but you entered YY.\n\nLisa, what should we do?");
                            
                        // Catch all other errors.
                        } else {
                            $("#Error_Text").html(data.ErrorText); // display errors
                            // $("#captcha_image").attr('src','securimage/securimage_show.php?' + Math.random()); // reload captcha image 
                            // WHY? return; // stop!
                        };
                    } else { 
                        alert("You seem to have entered the wrong security code.  Please try again.");
                        // $("#captcha_image").attr('src','securimage/securimage_show.php?' + Math.random()); // reload captcha image
                    };
                    
                    $("#captcha_image").attr('src','securimage/securimage_show.php?' + Math.random()); // reload captcha image
                    
				}; // endif captcha
                
			}, "json"); // end ajax call
   		}); // end #verify
//////////////


 	}); // end $(document).ready
    </script>
</head>

<body class="oneColFixCtrHdr">

<div id="container">
<div id="header"><h1>Home Energy Makeover<strong> Contest Entry</strong></h1></div>

<div id="mainContent">
    <p id="introHeader" style="display: none"><strong>Please answer the following questions to confirm that you’re eligible to enter
        the Home Energy Makeover contest. You may be asked to provide information from your
        gas and/or electric bills from the last 12 months</strong></p>

    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="HER Request" id="HEMO Contest Entry">
    <input type="hidden" name="referrer" value="<?php if (isset($_POST['referrer'])){echo $_POST['referrer'];}?>" />

    <div id="hear" class="questions" style="display: none">
        <h3>How did you hear about us?</h3>
            <input name="hearselect" type="radio" id="hear_bls" value="Better Living Show"
                <?php if($_POST['hearselect'] == 'Better Living Show'){ echo "checked=\"checked\"";}?> />Better Living Show<br />
            <input name="hearselect" type="radio" id="hear_ubi" value="Utility bill insert"
                <?php if($_POST['hearselect'] == 'Utility bill insert'){ echo "checked=\"checked\"";}?> />Utility bill insert<br />
            <input name="hearselect" type="radio" id="hear_un" value="Utility newsletter"
                <?php if($_POST['hearselect'] == 'Utility newsletter'){ echo "checked=\"checked\"";}?> />Utility newsletter<br />
            <input name="hearselect" type="radio" id="hear_newspaper" value="Ad in newspaper"
                <?php if($_POST['hearselect'] == 'Ad in newspaper'){ echo "checked=\"checked\"";}?> />Ad in newspaper<br />
            <input name="hearselect" type="radio" id="hear_radio" value="Ad on radio"
                <?php if($_POST['hearselect'] == 'Ad on radio'){ echo "checked=\"checked\"";}?> />Ad on radio<br />
            <input name="hearselect" type="radio" id="hear_web" value="Ad on Web site"
                <?php if($_POST['hearselect'] == 'Ad on Web site'){ echo "checked=\"checked\"";}?> />Ad on Web site<br />
            <input name="hearselect" type="radio" id="hear_flyer" value="Flyer"
                <?php if($_POST['hearselect'] == 'Flyer'){ echo "checked=\"checked\"";}?> />Flyer<br />
            <input name="hearselect" type="radio" id="hear_newsstory" value="News story"
                <?php if($_POST['hearselect'] == 'News story'){ echo "checked=\"checked\"";}?> />News story<br />
            <input name="hearselect" type="radio" id="hear_wom" value="Word-of-mouth"
                <?php if($_POST['hearselect'] == 'Word-of-mouth'){ echo "checked=\"checked\"";}?> />Word-of-mouth<br />
        <input name="in_hear" type="hidden" id="in_hear" value=<?php echo $_POST['in_hear'];?>/>
    </div>

    <div id="oregon" class="questions" style="display: none">
        <h3>Is your home in Oregon?</h3>
        <input name="oregon" type="button" value="Yes" id="button_oregon_yes"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="oregon" type="button" value="No" id="button_oregon_no"/>
        <input name="in_oregon" type="hidden" id="in_oregon" value=<?php echo $_POST['in_oregon'];?>/>
    </div>
    
    <div id="oregon_np" class="nq" style="display: none"><p>Your home must be located in Oregon to qualify.</p></div>
   
    <div id="own" class="questions" style="display: none">
        <h3>Do you own your home?</h3>
        <input name="own" type="button" value="Yes" id="button_own_yes"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="own" type="button" value="No" id="button_own_no"/>
        <input name="own_home" type="hidden" id="own_home" value=<?php echo $_POST['own_home'];?>/>
    </div>
    
    <div id="own_nq" class="nq" style="display: none">
        <p>We’re sorry. You must own your home to be eligible for the Home Energy Makeover Contest.</p>
        <p>To learn how to save energy in your rented home, see
            <a href="http://www.energytrust.org/residential/existinghomes/renters.html">the low-cost,
            no-cost energy-saving tips on our Web site</a>. We also offer ideas for how you can encourage
            your landlord to make energy-saving improvements that may qualify for Energy Trust incentives.</p>
    </div>

    <div id="singleFamily" class="questions" style="display: none">
        <h3>Is your home a <strong>single-family </strong> home?</h3>
        <p>NOTE: Duplexes, triplexes, apartments and condos do not qualify as single-family homes.</p>
        <input name="single" type="button" value="Yes" id="button_mf_yes"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="single" type="button" value="No" id="button_mf_no"/>
        <input name="single_family" type="hidden" id="single_family" value=<?php echo $_POST['single_family'];?>/>
    </div>
    
    <div id="mf_nq" class="nq" style="display: none">
        <p>We’re sorry.&nbsp; Only single-family, owner-occupied homes are &nbsp;eligible for the Home Energy Makeover Contest.</p>
        <p>To learn how to make your multifamily home more energy efficient, visit Energy Trust’s Web site or call 1-866-311-1822.
            You may qualify for cash-back incentives from Energy Trust and Oregon energy tax credits.</p>
    </div>

    <div id="primary" class="questions" style="display: none">
        <h3><strong>Is your single-family home your primary residence?</strong></h3>
        <input name="primary" type="button" value="Yes" id="button_primary_yes"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="primary" type="button" value="No" id="button_primary_no"/>
        <input name="primary_residence" type="hidden" id="primary_residence" value=<?php echo $_POST['primary_residence'];?>/>
        <div id="oregon_np2" class="nq" style="display: none">
            <p>We’re sorry. To enter the Home Energy Makeover Contest, your Oregon single-family home must be your primary residence.</p>
            <p>If you own your home as a rental or second home, you may be eligible for Energy Trust programs and cash incentives
                to make energy efficient improvements. For details, visit our Web site.</p>
        </div>
    </div>

    <div id="heat" class="questions" style="display: none">
        <h3>What is your primary heating fuel?</h3>
        
        <p>
          <label>
            <input name="heat" type="radio" id="heat_electric" value="electric"
                <?php if($_POST['heat'] == 'electric'){ echo "checked=\"checked\"";}?> />Electricity </label>(Heat Pump, Electric Furnace, Baseboard, Wall Units)<br />
          <label>
            <input type="radio" name="heat" value="gas" id="heat_gas"
                <?php if($_POST['heat'] == 'gas'){ echo "checked=\"checked\"";}?>/>Natural Gas</label>(Natural Gas Furnace or Direct Vent Gas Heaters)<br />
          <label>
            <input type="radio" name="heat" value="other" id="heat_other"
                <?php if($_POST['heat'] == 'other'){ echo "checked=\"checked\"";}?>/>Other</label>(Oil, Propane, Wood)<br /></p>
    </div>
    
    <div id="heat_nq" class="nq" style="display: none">
        <p>We’re sorry. Only homes heated with electricity or natural gas are eligible for the Home Energy Makeover Contest.</p>
        <p>If you’re an Oregon customer of Portland General Electric (PGE) or Pacific Power living in a home heated with oil,
        propane, kerosene, butane or wood, you may be able to weatherize your home and receive free energy-saving light
        bulbs with help from the Oregon Department of Energy’s State Home Oil Weatherization (SHOW) program.</p>
    
        As an Oregon PGE or Pacific Power customer, you qualify for Energy Trust incentives when you make the following home improvements:
        <ul type="disc">
            <li><a href="water_heater.html">High-efficiency electric water heater</a></li>
            <li><a href="/residential/es/products/promotions_swas.html">ENERGY STAR clothes washer</a></li>
            <li><a href="/residential/es/products/promotions_sitf.html">ENERGY STAR refrigerator</a></li>
            <li><a href="/solar/residential/index.html">Solar electric system</a></li>
            <li><a href="/solar/water/index.html">Solar water heating system</a></li>
        </ul>
            
        <p><a href="/residential/existinghomes/forms/LowCostRentersChecklist_SHOW.pdf" target="_blank">We also offer
            <u>low</u>-cost, no-cost tips to help you start saving energy now</a></p>
    </div>

    <div id="utility_gas" class="questions" style="display: none">
        <h3>Which utility provides gas service to your home?</h3>

        <p><label><input type="radio" name="utility_gas" value="CNG" id="utility_gas_cascade"
            <?php if($_POST['utility_gas'] == 'CNG'){ echo "checked=\"checked\"";}?>/>Cascade Natural</label><br />
        <label><input type="radio" name="utility_gas" value="NWN" id="utility_gas_nwn"
            <?php if($_POST['utility_gas'] == 'NWN'){ echo "checked=\"checked\"";}?>/>NW Natural</label><br />
        <label><input type="radio" name="utility_gas" value="other" id="utility_gas_other"
            <?php if($_POST['utility_gas'] == 'other'){ echo "checked=\"checked\"";}?>/>Other</label></p>
    </div>

    <div id="heat_np" class="nq" style="display: none">
        <p>We’re sorry. Only homes heated with gas from NW Natural or Cascade Natural Gas are eligible for the
        Home Energy Makeover Contest.</p>
    
        <p>For information on energy-saving programs available for your home, please contact your local natural gas
        provider. If you have questions, please call us at 1-866-368-7878.</p>
    </div>
    
    <div id="utility_electric" class="questions" style="display: none">
        <h3>Which utility provides electric service to your home?</h3>
        <p>
        <label><input type="radio" name="utility_electric" value="PGE" id="utility_electric_pge"
            <?php if($_POST['utility_electric'] == 'PGE'){ echo "checked=\"checked\"";}?>/>Portland General Electric</label><br />
        <label><input type="radio" name="utility_electric" value="PAC" id="utility_electric_pac"
            <?php if($_POST['utility_electric'] == 'PAC'){ echo "checked=\"checked\"";}?>/>Pacific Power</label><br />
        <label><input type="radio" name="utility_electric" value="other" id="utility_electric_other"
            <?php if($_POST['utility_electric'] == 'other'){ echo "checked=\"checked\"";}?>/>Other</label><br />
        </p>
        
        <div id="heat_np2" class="nq" style="display: none">
            <p>We’re sorry. Only homes heated with electricity from Portland General Electric or Pacific Power are
                eligible for the Home Energy Makeover Contest.</p>
            <p>For information on energy-saving programs available for your home, please contact your local electricity
            provider. If you have questions, please call us at 1-866-368-7878.</p>
        </div>
    </div>

    <div id="form" style="display: none">
        <table id="formtable" width="100%" border="0" cellpadding="10" bordercolor="#FFCC33">
        <tr>
            <td bgcolor="#FFFFCC" class="error">Please provide all the information requested in the form below. In
                some cases, you may be asked to enter your energy usage information for the last 12 months. This
                occurs if we are unable to access the data from your utility records. To provide usage information,
                you will need to refer to your electric and/or gas utility bills.
                <span style="color: red;"><em><div id="Error_Text"><?php echo $Error_Text;?></div></em></span></td>
        </tr>

        <tr><td>
            <table width="600" border="0" align="center" cellpadding="3" cellspacing="0">
                <tr>
                    <td width="45%"><font face="Verdana, Arial, Helvetica, sans-serif" size="-1">First Name:</font><font face="Verdana, Arial, Helvetica, sans-serif"><br />
                        <input type="text" name="FirstName" value="<?php if (isset($_POST['FirstName'])){echo $_POST['FirstName'];}?>" /></font></td>
                    <td colspan="3"><font face="Verdana, Arial, Helvetica, sans-serif" size="-1">Last Name:</font><font face="Verdana, Arial, Helvetica, sans-serif"><br />
                        <input type="text" name="LastName" value="<?php if (isset($_POST['LastName'])) {echo $_POST['LastName'];}?>" /></font></td>
                </tr>

                <tr>
                    <td colspan="3"><p><font face="Verdana, Arial, Helvetica, sans-serif" size="-1">Street Address:<br /></font>
                        <font face="Verdana, Arial, Helvetica, sans-serif"><input type="text" name="address" size="50" id="address"
                            value="<?php if (isset($_POST['address'])){echo $_POST['address'];}?>" /></font><br />
                        <font face="Verdana, Arial, Helvetica, sans-serif"><input name="address2" type="text" id="address2" size="50"
                            value="<?php if (isset($_POST['address2'])){echo $_POST['address2'];}?>"/></font></p></td>
                </tr>

                <tr>
                    <td width="45%"><font face="Verdana, Arial, Helvetica, sans-serif" size="-1">City:<br /></font>
                        <font face="Verdana, Arial, Helvetica, sans-serif"><input type="text" name="city" size="30" id="city"
                            value="<?php if (isset($_POST['city'])){echo $_POST['city'];}?>" /></font></td>
                    <td width="27%"><font face="Verdana, Arial, Helvetica, sans-serif" size="-1">State:<br /></font>
                        <input type="text" name="state" size="10" id="state"
                            value="<?php if (isset($_POST['state'])){echo $_POST['state'];}?>" /></td>
                    <td width="28%"><font face="Verdana, Arial, Helvetica, sans-serif" size="-1">Zip Code:<br /></font>
                        <input name="ZipCode" type="text" size="10" maxlength="10" id="ZipCode"
                            value="<?php if (isset($_POST['ZipCode'])){echo $_POST['ZipCode'];}?>" /></td>
                </tr>

                <tr>
                    <td colspan="3"><font face="Verdana, Arial, Helvetica, sans-serif" size="-1">Phone Number:<br /></font>
                        <font face="Verdana, Arial, Helvetica, sans-serif"><input type="text" name="phone" size="50" id="phone"
                            value="<?php if (isset($_POST['phone'])){echo $_POST['phone'];}?>" />Example: 503-445-1234</font></td>
                </tr>

                <tr>
                    <td colspan="3"><font face="Verdana, Arial, Helvetica, sans-serif" size="-1">Email Address:<br /></font>
                        <font face="Verdana, Arial, Helvetica, sans-serif"><input type="text" name="email" size="50" id="email"
                            value="<?php if (isset($_POST['email'])){echo $_POST['email'];}?>" /></font></td>
                </tr>

                <tr><td colspan="3" bgcolor="#FFFFCC"><p align="center"><strong>About Your Home</strong></p></td></tr>

                <tr>
                    <td><font face="Verdana, Arial, Helvetica, sans-serif" size="-1">Year built:&nbsp;&nbsp; </font>
                        <font face="Verdana, Arial, Helvetica, sans-serif"><input type="text" name="yearBuilt" size="4" id="yearBuilt"
                        value="<?php if (isset($_POST['yearBuilt'])){echo $_POST['yearBuilt'];}?>" /></font></td>
                    <td colspan="2" valign="top"><font face="Verdana, Arial, Helvetica, sans-serif" size="-1">Square Feet:&nbsp;&nbsp; </font>
                        <font face="Verdana, Arial, Helvetica, sans-serif"><input type="text" name="squareFeet" size="4" id="squareFeet"
                        value="<?php if (isset($_POST['squareFeet'])){echo $_POST['squareFeet'];}?>" /> (include only finished, heated space)</font></td>
                </tr>

                <tr><td colspan="3">&nbsp;</td></tr>

                <tr>
                    <td colspan="3"><div id="captcha"><img id="captcha_image" src="securimage/securimage_show.php" alt="CAPTCHA Image" /><a href="#" onclick="document.getElementById('captcha_image').src = 'securimage/securimage_show.php?' + Math.random(); return false"><img id="captch_refresh" src="img/refresh.gif" border="0" alt="Reload Image"/></a>
                    <font face="Verdana, Arial, Helvetica, sans-serif" size="-1">Security Code:</font> <input type="text" name="code" size="8" /></div></td>
                </tr>

                <tr><td colspan="3"><div id="results"></div></td></tr>
            </table></td>
        </tr>

        <tr><td  align="center" bgcolor="#FFFFCC"><input type="button" name="verify" id="verify" value="Continue" /></td></tr>
    </table>

    <div id="meter_readings" class="question">
        <div style="background-color: #FFFFCC; min-height: 50px; padding: 5px 5px 5px 5px; vertical-align: middle" class="error"><p>We're sorry.  We are unable to locate a record of your utility usage. In order to qualify for
            the contest, please refer to your utility bills and provide information for the last
            12 months below:</p></div>
        
        <h3>Please enter the amount of gas (therms) or electricity (kWh) you used in the last 12 months.</h3>
        
        <p>Wondering what to look for? <a href="#">See sample electric and gas bills</a></p>

        <table border="0" cellpadding="2" align="center"> 
        <tr>
            <td valign="top" style="border: solid 1px black" id="electric_readings_table">
                <table width="350" border="0">
                <caption><h3>Electric Utility Readings</h3></caption>
                <tr>
                    <th colspan="2" scope="col" nowrap><label>Electric Utility Account Number:&nbsp;
                        <input type="text" name="electric_account_number" style="width: 150px;" id="electric_account_number" /></label></th>
                </tr>
                <tr>
                    <th colspan="2" scope="col"><label>Electric Utility:<br />
                        <select name="electric_provider" id="electric_provider">
                            <?php do {  
                                echo "<option value=\"".$row_electricProviders['id']."\"";
                                if (!(strcmp($row_gasProviders['id'], $_POST['electric_provider']))) {
                                    echo " SELECTED";
                                }
                                echo "> ";
                                echo $row_electricProviders['name']."</option>";
                            } while ($row_electricProviders = mysql_fetch_assoc($electricProviders));  //end do
                            $rows = mysql_num_rows($electricProviders);
                            if($rows > 0) {  //if there are rows, reset the rowset to the first record
                                mysql_data_seek($electricProviders, 0);
                                $row_electricProviders = mysql_fetch_assoc($electricProviders);
                            } //endif
                            ?>
                        </select></label></th>
                </tr>
                <tr>
                    <th scope="col">Meter Reading Date </th>
                    <th scope="col">Usage (kWh)</th>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="ele_month1" id="ele_month1" class="date-pick" value="<?php if (isset($_POST['ele_month1'])){echo $_POST['ele_month1'];}?>"/></th>
                    <td><input type="text" name="ele_reading1" id="ele_reading1" style="width: 100px" value="<?php if (isset($_POST['ele_reading1'])){echo $_POST['ele_reading1'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="ele_month2" id="ele_month2" class="date-pick" value="<?php if (isset($_POST['ele_month2'])){echo $_POST['ele_month2'];}?>"/></th>
                    <td><input type="text" name="ele_reading2" id="ele_reading2" style="width: 100px" value="<?php if (isset($_POST['ele_reading2'])){echo $_POST['ele_reading2'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="ele_month3" id="ele_month3" class="date-pick" value="<?php if (isset($_POST['ele_month3'])){echo $_POST['ele_month3'];}?>"/></th>
                    <td><input type="text" name="ele_reading3" id="ele_reading3" style="width: 100px" value="<?php if (isset($_POST['ele_reading3'])){echo $_POST['ele_reading3'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="ele_month4" id="ele_month4" class="date-pick" value="<?php if (isset($_POST['ele_month4'])){echo $_POST['ele_month4'];}?>"/></th>
                    <td><input type="text" name="ele_reading4" id="ele_reading4" style="width: 100px" value="<?php if (isset($_POST['ele_reading4'])){echo $_POST['ele_reading4'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="ele_month5" id="ele_month5" class="date-pick" value="<?php if (isset($_POST['ele_month5'])){echo $_POST['ele_month5'];}?>"/></th>
                    <td><input type="text" name="ele_reading5" id="ele_reading5" style="width: 100px" value="<?php if (isset($_POST['ele_reading5'])){echo $_POST['ele_reading5'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="ele_month6" id="ele_month6" class="date-pick" value="<?php if (isset($_POST['ele_month6'])){echo $_POST['ele_month6'];}?>"/></th>
                    <td><input type="text" name="ele_reading6" id="ele_reading6" style="width: 100px" value="<?php if (isset($_POST['ele_reading6'])){echo $_POST['ele_reading6'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="ele_month7" id="ele_month7" class="date-pick" value="<?php if (isset($_POST['ele_month7'])){echo $_POST['ele_month7'];}?>"/></th>
                    <td><input type="text" name="ele_reading7" id="ele_reading7" style="width: 100px" value="<?php if (isset($_POST['ele_reading7'])){echo $_POST['ele_reading7'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="ele_month8" id="ele_month8" class="date-pick" value="<?php if (isset($_POST['ele_month8'])){echo $_POST['ele_month8'];}?>"/></th>
                    <td><input type="text" name="ele_reading8" id="ele_reading8" style="width: 100px" value="<?php if (isset($_POST['ele_reading8'])){echo $_POST['ele_reading8'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="ele_month9" id="ele_month9" class="date-pick" value="<?php if (isset($_POST['ele_month9'])){echo $_POST['ele_month9'];}?>"/></th>
                    <td><input type="text" name="ele_reading9" id="ele_reading9" style="width: 100px" value="<?php if (isset($_POST['ele_reading9'])){echo $_POST['ele_reading9'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="ele_month10" id="ele_month10" class="date-pick" value="<?php if (isset($_POST['ele_month10'])){echo $_POST['ele_month10'];}?>"/></th>
                    <td><input type="text" name="ele_reading10" id="ele_reading10" style="width: 100px" value="<?php if (isset($_POST['ele_reading10'])){echo $_POST['ele_reading10'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="ele_month11" id="ele_month11" class="date-pick" value="<?php if (isset($_POST['ele_month11'])){echo $_POST['ele_month11'];}?>"/></th>
                    <td><input type="text" name="ele_reading11" id="ele_reading11" style="width: 100px" value="<?php if (isset($_POST['ele_reading11'])){echo $_POST['ele_reading11'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="ele_month12" id="ele_month12" class="date-pick" value="<?php if (isset($_POST['ele_month12'])){echo $_POST['ele_month12'];}?>"/></th>
                    <td><input type="text" name="ele_reading12" id="ele_reading12" style="width: 100px" value="<?php if (isset($_POST['ele_reading12'])){echo $_POST['ele_reading12'];}?>"/></td>
                </tr>
                </table>
            </td>

            <td valign="top" style="border: solid 1px black" id="gas_readings_table">
                <table width="340" border="0">
                <caption><h3>Gas Utility Readings</h3></caption>
                <tr>
                    <th colspan="2" scope="col"><label>I don't have gas service:&nbsp;<input type="checkbox" id="nogasutil"></label></th>
                </tr>
                <tr>
                    <th colspan="2" scope="col"><label>Gas Utility Account Number:&nbsp;
                    <input type="text" name="gas_account_number" id="gas_account_number" style="width: 150px" /></label></th>
                </tr>
                <tr>
                <th colspan="2" scope="col"><label>Gas Utility:<br />
                <select name="gas_provider" id="gas_provider">
                    <?php do {  
                        echo "<option value=\"".$row_gasProviders['id']."\"";
                        if (!(strcmp($row_gasProviders['id'], $_POST['gas_provider']))) {
                            echo " SELECTED";
                        }
                        echo "> ";
                        echo $row_gasProviders['name']."</option>";
                    } while ($row_gasProviders = mysql_fetch_assoc($gasProviders));  //end do
                        $rows = mysql_num_rows($gasProviders);
                        if($rows > 0) {  //if there are rows, reset the rowset to the first record
                            mysql_data_seek($electricProviders, 0);
                        $row_gasProviders = mysql_fetch_assoc($gasProviders);
                    } //endif
                    ?>
                </select>
                </label></th>
                </tr>
                <tr>
                    <th scope="col">Meter Reading Date </th>
                    <th scope="col">Usage (therms)</th>
                </tr>
                <tr>
                    <th scope="row"><input name="gas_month1" type="text" class="date-pick" id="gas_month1" value="<?php if (isset($_POST['gas_month1'])){echo $_POST['gas_month1'];}?>"/></th>
                    <td><input type="text" name="gas_reading1" id="gas_reading1" style="width: 100px" value="<?php if (isset($_POST['gas_reading1'])){echo $_POST['gas_reading1'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="gas_month2" id="gas_month2" class="date-pick" value="<?php if (isset($_POST['gas_month2'])){echo $_POST['gas_month2'];}?>"/></th>
                    <td><input type="text" name="gas_reading2" id="gas_reading2" style="width: 100px" value="<?php if (isset($_POST['gas_reading2'])){echo $_POST['gas_reading2'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="gas_month3" id="gas_month3" class="date-pick" value="<?php if (isset($_POST['gas_month3'])){echo $_POST['gas_month3'];}?>"/></th>
                    <td><input type="text" name="gas_reading3" id="gas_reading3" style="width: 100px" value="<?php if (isset($_POST['gas_reading3'])){echo $_POST['gas_reading3'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="gas_month4" id="gas_month4" class="date-pick" value="<?php if (isset($_POST['gas_month4'])){echo $_POST['gas_month4'];}?>"/></th>
                    <td><input type="text" name="gas_reading4" id="gas_reading4" style="width: 100px" value="<?php if (isset($_POST['gas_reading4'])){echo $_POST['gas_reading4'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="gas_month5" id="gas_month5" class="date-pick" value="<?php if (isset($_POST['gas_month5'])){echo $_POST['gas_month5'];}?>"/></th>
                    <td><input type="text" name="gas_reading5" id="gas_reading5" style="width: 100px" value="<?php if (isset($_POST['gas_reading5'])){echo $_POST['gas_reading5'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="gas_month6" id="gas_month6" class="date-pick" value="<?php if (isset($_POST['gas_month6'])){echo $_POST['gas_month6'];}?>"/></th>
                    <td><input type="text" name="gas_reading6" id="gas_reading6" style="width: 100px" value="<?php if (isset($_POST['gas_reading6'])){echo $_POST['gas_reading6'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="gas_month7" id="gas_month7" class="date-pick" value="<?php if (isset($_POST['gas_month7'])){echo $_POST['gas_month7'];}?>"/></th>
                    <td><input type="text" name="gas_reading7" id="gas_reading7" style="width: 100px" value="<?php if (isset($_POST['gas_reading7'])){echo $_POST['gas_reading7'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="gas_month8" id="gas_month8" class="date-pick" value="<?php if (isset($_POST['gas_month8'])){echo $_POST['gas_month8'];}?>"/></th>
                    <td><input type="text" name="gas_reading8" id="gas_reading8" style="width: 100px" value="<?php if (isset($_POST['gas_reading8'])){echo $_POST['gas_reading8'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="gas_month9" id="gas_month9" class="date-pick" value="<?php if (isset($_POST['gas_month9'])){echo $_POST['gas_month9'];}?>"/></th>
                    <td><input type="text" name="gas_reading9" id="gas_reading9" style="width: 100px" value="<?php if (isset($_POST['gas_reading9'])){echo $_POST['gas_reading9'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="gas_month10" id="gas_month10" class="date-pick" value="<?php if (isset($_POST['gas_month10'])){echo $_POST['gas_month10'];}?>"/></th>
                    <td><input type="text" name="gas_reading10" id="gas_reading10" style="width: 100px" value="<?php if (isset($_POST['gas_reading10'])){echo $_POST['gas_reading10'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="gas_month11" id="gas_month11" class="date-pick" value="<?php if (isset($_POST['gas_month11'])){echo $_POST['gas_month11'];}?>"/></th>
                    <td><input type="text" name="gas_reading11" id="gas_reading11" style="width: 100px" value="<?php if (isset($_POST['gas_reading11'])){echo $_POST['gas_reading11'];}?>"/></td>
                </tr>
                <tr>
                    <th scope="row"><input type="text" name="gas_month12" id="gas_month12" class="date-pick" value="<?php if (isset($_POST['gas_month12'])){echo $_POST['gas_month12'];}?>"/></th>
                    <td><input type="text" name="gas_reading12" id="gas_reading12" style="width: 100px" value="<?php if (isset($_POST['gas_reading12'])){echo $_POST['gas_reading12'];}?>"/></td>
                </tr>
                </table>
            </td>
        </tr>
        <tr><td colspan="2" align="center"><br /><input type="button" name="show_disclaimer" id="show_disclaimer" value="Continue"/></td></tr>
        </table>      
    </div> 
       
    <div id="disclaimer">
        <h3>Disclaimer</h3>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec pharetra. Pellentesque at nisi non
        enim pretium tempus. Aenean pulvinar libero sed odio. Proin et enim non urna tincidunt suscipit.
        Aliquam scelerisque mi a lorem. Duis imperdiet diam ac felis. Curabitur non erat ac urna hendrerit
        ornare. Sed pretium nisi eu leo. Praesent egestas. Maecenas laoreet, ligula sit amet consequat
        iaculis, libero nunc placerat elit, a volutpat odio nisl a urna. Donec dictum. Mauris felis.
        Praesent hendrerit pharetra magna. In convallis facilisis sapien. Ut mollis nibh tempor ante.
        Aenean tortor arcu, tincidunt vel, malesuada ut, gravida a, nunc.</p>

        <p>Nam nunc diam, ullamcorper quis, sollicitudin sit amet, posuere quis, tortor. Donec gravida nisi.
        Quisque rhoncus. Nullam non ligula. Cras hendrerit fringilla enim. Vivamus venenatis velit eget
        orci. Morbi pulvinar pretium turpis. Nulla porta. Proin dui. Donec eu sem. Nam a neque. Aliquam
        scelerisque lectus et arcu molestie eleifend. Vestibulum porttitor pretium velit. Class aptent
        taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam metus diam,
        mattis eu, sodales ut, venenatis in, lectus. Praesent magna ante, tincidunt sed, aliquet nec,
        commodo at, metus. Integer consequat lorem ut tortor. Maecenas risus ligula, tincidunt id,
        scelerisque ut, commodo ac, dui. Aenean blandit.</p>

        <p>Aenean suscipit consectetur ipsum. Donec et purus at lectus fringilla pulvinar. Phasellus libero
        lectus, vulputate ut, varius eget, vulputate id, risus. Vivamus gravida diam ut magna. Suspendisse
        hendrerit pretium sapien. Nullam malesuada. Vivamus venenatis leo at augue. Suspendisse venenatis,
        mi sed aliquam vestibulum, nisi eros blandit tellus, ac tincidunt enim tellus nec ante. Cras id
        ligula quis velit aliquet consectetur. Curabitur vitae diam. Pellentesque habitant morbi tristique
        senectus et netus et malesuada fames ac turpis egestas. Fusce vel enim sit amet purus egestas
        viverra. Sed dictum enim sit amet nunc. Aenean erat felis, dapibus nec, ornare ut, tempor
        pellentesque, tortor. Vestibulum a erat. Nunc in est. In odio. Vestibulum risus.</p>

        <p>Vestibulum id sem. Vivamus magna eros, iaculis at, fringilla ac, congue a, ante. Sed vitae nisl
        ac mi blandit porttitor. Aenean et tortor. Nulla in nulla non augue sagittis vehicula. Morbi in
        quam. Sed rhoncus porttitor ligula. Maecenas vitae dui vel ante tincidunt euismod. Etiam vehicula.
        Donec blandit elit nec eros. Morbi orci mauris, auctor id, rutrum a, porttitor eu, urna. Sed
        vulputate nibh et dui. Mauris mi lorem, pharetra a, viverra a, fermentum sed, est.</p>

        <p>Donec vitae magna. Suspendisse vestibulum. Mauris libero ante, suscipit vel, vehicula in, varius
        ut, arcu. Sed porta facilisis justo. Praesent pulvinar consectetur erat. Duis laoreet, enim nec
        venenatis gravida, leo augue tincidunt est, a ultrices neque urna ut eros. Vestibulum ipsum diam,
        rutrum et, facilisis mollis, viverra id, leo. Cras nibh. Fusce nisi. Integer purus nulla, blandit
        eu, sodales aliquet, ullamcorper facilisis, ipsum. Pellentesque ligula urna, gravida aliquam,
        pellentesque et, tincidunt quis, odio. Duis lorem. Maecenas at lacus. In dignissim urna et
        libero. Curabitur posuere sem eu lorem. Donec tempus ipsum. Cras augue diam, congue tempor,
        ultricies fringilla, feugiat quis, tortor. Suspendisse potenti. Nulla tellus dolor, ornare at,
        dapibus ut, ullamcorper in, libero. </p>
        
        <p align="center">
            <input type="submit" name="enter_contest"  id="enter_contest" value="I agree to the terms and conditions" />
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="button" name="not_enter_contest"  id="not_enter_contest" value="I DO NOT agree to the terms and conditions" />
        </p>
        
    </div>
    
    </div>

    </form>
    <?php 
	if($debug){
		echo "<pre>";
		print_r($result);
		echo "</pre>";

		$Provider = $result->getBarCodeAndProvidersResult->custUtilities->elecUtility;
		$Barcode = $result->getBarCodeAndProvidersResult->outBarCode;
		if(isset($Provider)){
			echo "The Electrical Provider is $Provider. <br />";
			echo "The Barcode is $Barcode. <br />"; 
		} // endif $Provider
	} // endif $debug
?>

	<!-- end #mainContent --></div>
  
<div id="thankyou" style="display: none">
    
    <br><br><br>
    <p style="padding: 10px 40px 10px 40px">Thank you for entering Energy Trust’s Home Energy Makeover Contest. Finalists will be
        selected after May 5th, and four winners will be chosen by June 1st. Watch for winner
        updates on this Web site beginning June 1st.  Good luck!</p>
    <br><br><br>
    
</div>
    
<div id="disagree" style="display: none">
    <br><br><br>
    <p style="padding: 10px 40px 10px 40px">This page is shown when somebody does not agree with the T&C.</p>
    <br><br><br>
</div>
    
<br />
  <div id="footer">
  <p>&nbsp;&nbsp;&nbsp;&copy; 2009 Energy Trust of Oregon
  <!-- end #footer --></div>
<!-- end #container --></div>
</body>
</html>
