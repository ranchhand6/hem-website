<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_makeover = "localhost";
$database_makeover = "eto_makeover";
$username_makeover = "makeover_user";
$password_makeover = "m@ke0v3r!";
$makeover_connection = mysql_connect($hostname_makeover, $username_makeover, $password_makeover) or die(mysql_error());
?>
