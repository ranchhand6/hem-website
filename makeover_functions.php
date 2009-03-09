<?php

require_once('Connections/makeover.php');

function sendProjects(){

	global $makeover_connection;
	global $database_makeover;
	global $debug;
	global $sendXML;

	$requestsSQL = "SELECT * FROM `requests` WHERE `transmitted_at` is null"; // select unsent requests
	
	mysql_select_db($database_makeover, $makeover_connection);
	$requestsResult = mysql_query($requestsSQL, $makeover_connection) or die(mysql_error());
	
	$ProjectID = "P00000264832";
	while($request_row =  mysql_fetch_assoc($requestsResult)){
	
		$eto_import = array(
			"ProjectID" =>  $ProjectID,
			"Generated" => date("c"),
			"Project"   => array(
				"Customer" 	=> array(
					"ProjectID"    => $ProjectID,
					"FirstName"    => $request_row['FirstName'],
					"LastName"     => $request_row['LastName'],
					"ContactName"  => $request_row['FirstName'] . " " .  $request_row['LastName'],
					"BillAddress1" => $request_row['address'],
					"BillCity"     => $request_row['city'],
					"BillState"    => $request_row['state'],
					"BillZip"      => $request_row['ZipCode'],
					"SiteAddress1" => $request_row['address'],
					"SiteCity"     => $request_row['city'],
					"SiteState"    => $request_row['state'],
					"SiteZip"      => $request_row['ZipCode'],
					"PhoneHome"    => $request_row['phone']
				), // end "Customer"
				"Site" => array(
					"SiteCollection" => "SF",
					"Address1"	  => $request_row['address'],
					"City"	      => $request_row['city'],
					"State"       => $request_row['state'],
					"Zip"         => $request_row['ZipCode'],
					"HeatSysFuel" => $request_row['heat'],
					"ProviderEle" => $request_row['utility_electric'],
					"ProviderGas" => $request_row['utility_gas'],
					"NoUnits"	  => 1,
					"ProjectID"   => $ProjectID,
					"SiteAttr"	  => array("ProjectID" => $ProjectID,
										   "Attr"      => "SFTYPE",
										   "Value"     => "Attached") // end array SiteAttr
				) // end "Site"
			) // end "Project"
		); // end $eto_import
	
		$readingsSQL = "SELECT * from `readings` where `type` = 'gas' and `request_id` = {$request_row['id']}";
		
		mysql_select_db($database_makeover, $makeover_connection);
		$readingResult = mysql_query($readingsSQL, $makeover_connection) or die(mysql_error());
		$totalRows_gasReadings = mysql_num_rows($readingResult);
		
		while($reading_row =  mysql_fetch_assoc($readingResult)){ // gas
		
			$gasReadings[] = array("Read" => array(
                                    		"Qty" => $reading_row['reading'],
                                    		"ReadDate" => $reading_row['date']));
		} // end while $reading_row
		
		if($totalRows_gasReadings > 0){
/*			$ProviderAccts[] = array("ProviderCode" => $request_row['utility_gas'],
									"Account"	=> $request_row['gas_account_number'],
									"Readings"  => $gasReadings);
*/
			$ProviderAccts[] = array("ProviderCode" => $request_row['utility_gas'],
									"Account"	=> $request_row['gas_account_number']);
			$ProviderAccts[] = $gasReadings;
		}

		$readingsSQL = "SELECT * from `readings` where `type` = 'electric' and `request_id` = {$request_row['id']}";
		
		mysql_select_db($database_makeover, $makeover_connection);
		$readingResult = mysql_query($readingsSQL, $makeover_connection) or die(mysql_error());
		$totalRows_gasReadings = mysql_num_rows($readingResult);
		
		while($reading_row =  mysql_fetch_assoc($readingResult)){ // electric
		
			$electricReadings[] = array("Read" => array(
                                    		"Qty" => $reading_row['reading'],
                                    		"ReadDate" => $reading_row['date']));
		} // end while $reading_row
		
		if($totalRows_gasReadings > 0){
			$ProviderAccts[] = array("ProviderCode" => $request_row['utility_electric'],
									      "Account" => $request_row['electric_account_number'],
									     "Readings" => $electricReadings);

		}
		
		$eto_import['Project']['Site']['ProviderAcct'] = $ProviderAccts;
		
		if($debug){
			echo "<pre>";
			print_r($eto_import);
			echo "</pre>";
		} // endif $debug
		
		$xml = new XmlWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('ETOImport');
		
		write($xml, $eto_import);
		
		$xml->endElement();
		
		$xml_string = $xml->outputMemory(true);
	
		if($sendXML){ // if we're in send mode	
			$eto_soap = new SoapClient("https://forms.energytrust.org/AddrProvider.asmx?WSDL");
			
			$params = array('xmlstring' => $xml_string); // end array $params
			
			try{
				$result = $eto_soap->sendToImportQueue($params);
			} catch (Exception $e) {
				echo 'Caught exception: ',  $e->getMessage(), "<br />";
				echo $eto_soap->__getLastRequest ();
			} // end try
		}else{
			echo  $xml->outputMemory(true); // output to page
			$result = true;
		} // endif $sendXML
		
		if($result){ // if transmission successful
			// update record transmission date so that it's not sent again
			$updateRequestSQL = "UPDATE `requests` SET `transmitted_at` = NOW() WHERE `id` = {$request_row['id']}";
			mysql_select_db($database_makeover, $makeover_connection);
			$updateResult = mysql_query($updateRequestSQL, $makeover_connection) or die(mysql_error());
		} // endif $result
	} // end while
	return array("result" => $result, "xml" => $xml_string);
} // end function sendProjects()

function write(XMLWriter $xml, $data){
	foreach($data as $key => $value){
		if(is_array($value)){
			if(!is_int($key)){ // ignore integer keys
				$xml->startElement($key);
			} // endif is_int
			write($xml, $value);
			if(!is_int($key)){ // ignore integer keys
				$xml->endElement();
			} // endif is_int
			continue;
		}
		$xml->writeElement($key, $value);
	} // end foreach
} // end function write()
?>
