<?php require_once('Connections/kiosk.php'); ?>
<?php

$Email_To = "lisa@delaris.com"; // separate email addresses with commas
//$Email_To = "tony@delaris.com, lisa@delaris.com"; // separate email addresses with commas
$subject = "Home Energy Makeover Contents Entry Link Request";


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>Home Energy Makeover Contest Kiosk Entry</title>
<link href="/Styles/makeover.css" rel="stylesheet" type="text/css" /></head>

<body class="oneColFixCtrHdr">

<div id="container">
  <div id="header">
    <h1>Home Energy Makeover<strong> Contest Entry</strong></h1>
  <!-- end #header --></div>
  <div id="mainContent">
<div class="right">  
<h1><strong>WANT TO WIN A HOME ENERGY MAKEOVER?</strong></h1>
<p>Energy Trust of Oregon invites you to enter the Home Energy Makeover Contest. 
  Winning homes will receive energy-saving improvements worth up to $25,000.</p>
<p>You can enter if you:</p>
<ul>
  <li>Own a single-family home Oregon as your primary residence</li>
  <li>Heat your home primarily with fuel from Pacific Power, Portland General Electric<u>,</u> NW Natural or Cascade Natural Gas </li>
</ul>
<form action="send.php" method="post" name="HEM Contest Entry Link Request" id="HEMlink">

<p><strong>Please enter your e-mail address below to receive a link to the online contest entry form: </strong></p>
<p align="center">E-mail Address:  
  <input name="customer_mail" type="text" id="customer_mail" size="50"></p>
<p align="center"><input name="Submit" type="submit" value="Submit" /></p></form>
<p>Your e-mail address will be kept strictly confidential. Except as required by law or to meet regulatory requirements, Energy Trust will maintain confidentiality of all information submitted to Energy Trust in connection with this website.</p></div>
<div class="left">
<p>NO PURCHASE NECESSARY TO ENTER OR WIN. To be eligible, entrant must own a detached, single-family home in Oregon that is heated primarily with fuel from Portland General Electric, Pacific Power, NW Natural or Cascade Natural Gas. Winning homes will be selected at the sole discretion of the sponsor based on energy usage and potential to demonstrate comprehensive energy savings. Contest is sponsored by Energy Trust of Oregon, Inc., 851 SW Sixth Ave., Suite 1200, Portland, OR 97204. Winners will be chosen on June 15, 2009. &nbsp;</p>
<p>For complete contest rules, go to <a href="rules.php">HomeEnergyMakeover.com/rules</a>. </p>
</div></div></div>
</body></html>