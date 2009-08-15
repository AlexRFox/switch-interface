<?php

require ("common.php");
require ("/var/switch-interface/password.php");

pstart ();

$str = "";

foreach ($switches as $id => $sw) {
	$str .= sprintf ("<h1>Switch %d</h1><br />", $id);

	$str .= "<pre>";
	$sw->conn = ftp_connect ($sw->ip);
	$login_result = ftp_login ($sw->conn, "admin", $password);
	if (!$login_result) {
		echo ("ftp connection failed\n");
		pfinish ();
	}

	$t = sprintf ("/tmp/curswitchconfig%d", $id);
	$config = fopen ($t, "w");

	$download_result = ftp_fget ($sw->conn, $config, "config", FTP_ASCII);
	if (!$download_result) {
		echo ("ftp download failed\n");
		pfinish ();
	}

	$str .= file_get_contents ($t);

	$str .= "</pre>";
}

echo ($str);
	
pfinish ();

?>
