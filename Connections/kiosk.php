<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_makeover = "localhost";
$database_makeover = "hem_kiosk";
$username_makeover = "hem_user";
$password_makeover = "cl3verbyh@lf!";
$makeover_connection = mysql_connect($hostname_makeover, $username_makeover, $password_makeover) or die(mysql_error());
?>
