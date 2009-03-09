<?php 

require_once("makeover_functions.php");

if(isset($_POST['submit'])){

	$params = new stdClass;
	
	$eto_import = array(
		"ProjectID" => "12345",
		"Generated" => date("c"),
		"Customer" 	=> array(
			"ProjectID" => "12345",
			"FirstName" => "Tony",
			"LastName"  => "Dennis",
			"ContactName" => "Joe Test"
		), // end "Customer"
		"Site" => array(
			"SiteCollection" => "SF",
			"Address1"	=> "830 SW Vincent Place",
			"City"	=> "Portland",
			"State" => "OR",
			"Zip" => "97239"
		) // end "Site"
	); // end $eto_import

	$xml = new XmlWriter();
	$xml->openMemory();
	$xml->startDocument('1.0', 'UTF-8');
	$xml->startElement('ETOImport');
	
	function write(XMLWriter $xml, $data){
		foreach($data as $key => $value){
			if(is_array($value)){
				$xml->startElement($key);
				write($xml, $value);
				$xml->endElement();
				continue;
			}
			$xml->writeElement($key, $value);
		}
	}
	write($xml, $eto_import);
	
	$xml->endElement();
	
} // endif isset eto_import

if(isset($_POST['get_barcode_and_provider'])){
	echo "Attempting to make soap client<br />";
	
	$eto_soap = new SoapClient("https://forms.energytrust.org/AddrProvider.asmx?WSDL");
	
	$params = array('streetaddress1' => $_POST['address1'],
					'city'           => $_POST['city'],
					'state'          => $_POST['state'],
					'zipCode'	     => $_POST['zip']
					); // end array $params

	try{
		$result = $eto_soap->getBarCodeAndProviders($params);
	} catch (Exception $e) {
    	echo 'Caught exception: ',  $e->getMessage(), "<br />";
		echo $eto_soap->__getLastRequest ();
	}

} // endif Get_Barcode_And_Provider

if(isset($_POST['run_request_queue'])){

	$debug = true; 	  // set to false for production
	$sendXML = true; // set to true for production
	
	$result_array = sendProjects();
	
	if($result_array['result']){ // success
		ini_set("sendmail_from", "hem@energytrust.org");
		mail("lisa@delaris.com", "integration succeeded", "XML -> " . $result_array['xml']);
	}else{ // failure
		ini_set("sendmail_from", "hem@energytrust.org");
		mail("lisa@delaris.com", "integration failed", "XML -> " . $result_array['xml']);
	}
} // endif isset($_POST['run_request_queue'])
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Add Makeover</title>
</head>

<body>
<form id="eto_import" name="eto_import" method="post" action="">
  <label>Send ETO Import:
  <input type="submit" name="submit" id="submit" value="Submit" />
  </label>
  <p>SOAP Get Barcode: 
    <input type="submit" name="get_barcode_and_provider" id="get_barcode_and_provider" value="Get_Barcode_And_Provider" />
  </p>
  <p>
    <label>Run Request Queue
    <input type="submit" name="run_request_queue" id="run_request_queue" value="Run_Request_Queue" />
    </label>
  </p>
  <p>
    <label>Address Line 1:
    <input type="text" name="address1" id="address1" value="<?php echo $_POST['address1']; ?>"/>
    </label>
    <br />
    <label>City:
    <input type="text" name="city" id="city" value="<?php echo $_POST['city']; ?>"/>
    </label>
    <br />
    <label>State:
    <input type="text" name="state" id="state" value="<?php echo $_POST['state']; ?>"/>
    </label>
    <br />
    <label>Zip:
    <input type="text" name="zip" id="zip" value="<?php echo $_POST['zip']; ?>"/>
    </label>
  </p>
</form>
<pre>
<?php 
print_r($eto_import); 
//echo $xml->outputMemory(true);
echo "Params: <br /";
print_r($params);
echo "Result:<br />";
print_r($result);

$Provider = $result->getBarCodeAndProvidersResult->custUtilities->elecUtility;
$Barcode = $result->getBarCodeAndProvidersResult->outBarCode;
echo htmlentities($xml_string);
?>
</pre>
<?php
if(isset($Provider)){
	echo "The Electrical Provider is $Provider. <br />";
	echo "The Barcode is $Barcode. <br />"; 
}
?>
</body>
</html>
