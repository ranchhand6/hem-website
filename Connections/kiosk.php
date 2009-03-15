<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_kiosk = "localhost";
$database_kiosk = "hem_kiosk";
$username_kiosk = "hem_user";
$password_kiosk = "cl3verbyh@lf!";
$kiosk_connection = mysql_connect($hostname_kiosk, $username_kiosk, $password_kiosk) or die(mysql_error());
?>
