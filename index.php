<?php

require ("common.php");
require ("/var/switch-interface/password.php");

pstart ();

$id = @$_REQUEST['id'];
$bandwidth = @$_REQUEST['bandwidth'];
$update = @$_REQUEST['update'];

if ($bandwidth && $id) {
	$stmt = sprintf ("update comps set bandwidth='%s' where id=%d",
			 $bandwidth, $id);
	query ($stmt);

	do_commit ();

	redirect ("index.php");
}

if ($update) {
	update_router ();
	redirect ("index.php");
}

function notblank ($val) {
	if (trim($val) == "") {
		return ("&nbsp;");
	} else {
		return ($val);
	}
}

function update_router () {
	global $password;

	$stmt = "select name, switch, port, bandwidth from comps order by port";
	$q = query ($stmt);
	
	global $switches;

	foreach ($switches as $id => $sw) {
		$sw->conn = ftp_connect ($sw->ip);
		$login_result = ftp_login ($sw->conn, "admin", $password);
		if (!$login_result) {
			echo ("ftp connection failed\n");
			pfinish ();
		}

		$t = sprintf ("/tmp/switchconfig%d", $id);
		
		$sw->fp = fopen ($t, "w");

		$sw->config = "";

		$sw->fp= fopen ($t, "w");
		$sw->config = "";
		$sw->config .= "vlan 1\n"
			."  name 1\n"
			."  normal \"\"\n"
			."  fixed 1-8\n"
			."  forbidden \"\"\n"
			."  untagged 1-8\n"
			."  ip address default-management";
		$sw->config .= sprintf (" %s 255.255.255.0\n",
					$sw->ip);
		$sw->config .= "exit\n";
	}

	global $low_speed;
	while (($r = fetch ($q)) != NULL) {
	        $switches[$r->switch]->config
			.= sprintf ("interface port-channel %d\n", $r->port);
		$switches[$r->switch]->config .= sprintf ("  name %s\n",
							  $r->name);
		if ($r->bandwidth == "high") {
			$switches[$r->switch]->config
				.= sprintf ("  no bandwidth-limit ingress\n"
					    ."  no bandwidth-limit egress\n");
		} else {
			$switches[$r->switch]->config
				.= sprintf ("  bandwidth-limit ingress\n"
					    ."  bandwidth-limit ingress %d\n"
					    ."  bandwidth-limit egress\n"
					    ."  bandwidth-limit egress %d\n",
					    $low_speed, $low_speed);
		}
		$switches[$r->switch]->config .= sprintf ("exit\n");
	}

	foreach ($switches as $id => $sw) {
		$sw->config .= "bandwidth-control\n";
		
		fwrite ($sw->fp, $sw->config);
		fclose ($sw->fp);
		
		$t = sprintf ("/tmp/switchconfig%d", $id);
		$upload = ftp_put ($sw->conn, "config", $t,
				   FTP_ASCII);
		
		if (!$upload) {
			echo ("ftp upload failed");
			pfinish ();
		}
		
		ftp_close ($sw->conn);
	}
	
	$_SESSION['flash'] = sprintf ("router update in progress, please"
				      ." wait at least 20 seconds before"
				      ." updating again.<br />");
}

$stmt = sprintf ("select name, ip, port, switch, bandwidth, id from comps"
		 ." order by switch, port");
$q = query ($stmt);
$rows = "";
$rownum = 0;

while (($r = fetch ($q)) != NULL) {
	$rownum++;
	$o = odd_even ($rownum);
	$rows .= "<tr $o>";
	foreach ($cols as $col) {
		$val = $r->$col;
		$rows .= sprintf ("<td>%s</td>", $val);
	}

	$rows .= "<td>";
	if ($r->bandwidth == "high") {
		$rows .= sprintf ("<a href='index.php"
				  ."?bandwidth=high&amp;id=%d'"
				  ." class='selected'>High</a> | ", $r->id);
		$rows .= sprintf ("<a href='index.php"
				  ."?bandwidth=low&amp;id=%d'>"
				  ."Low</a>", $r->id);
	} else {	
		$rows .= sprintf ("<a href='index.php"
				  ."?bandwidth=high&amp;id=%d'>"
				  ."High</a> | ", $r->id);
		$rows .= sprintf ("<a href='index.php"
				  ."?bandwidth=low&amp;id=%d'"
				  ." class='selected'>Low</a>", $r->id);	
	}
	$rows .= "</td>";

	$rows .= sprintf ("<td><a href='details.php?id=%d'>Details</a></td>",
			  $r->id);
	$rows .= "</tr>";
}

echo ("<a href='edit.php'>Create new</a> | ");
echo ("<a href='curconfig.php'>Current Switch Config</a>\n");

echo ("<div style='padding-top:1em'></div>\n");
echo ("<table class='boxed'>\n");
echo ("<tr>\n");
foreach ($cols as $col) {
	echo (sprintf ("<th>%s</th>\n", h($col)));
}
echo ("<th>bandwidth</th>");
echo ("<th>op</th>");
echo ("</tr>\n");

echo ($rows);

echo ("</table>\n");

echo ("<form action='index.php'>");
echo ("<input type='hidden' name='update' value='1' />");
echo ("<input type='submit' value='Update Router' />");
echo ("</form>");

echo ("<div style='padding-top:1em'></div>\n");

pfinish ();

?>
