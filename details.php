<?php

require ("common.php");

pstart ();

$id = 0 + @$_REQUEST['id'];
$bandwidth = @$_REQUEST['bandwidth'];

if ($bandwidth && $id) {
	$stmt = sprintf ("update comps set bandwidth='%s' where id=%d",
			 $bandwidth, $id);

	query ($stmt);
	
	do_commit ();
	
	$t = sprintf ("details.php?id=%d", $id);

	redirect ($t);
}

echo ("<div style='padding-top:1em'></div>\n");

if ($id) {
	$stmt = sprintf ("select name, ip, port, switch, bandwidth, id"
			 ." from comps where id=%d", $id);
	$q = query ($stmt);

	
	if (($r = fetch ($q)) == NULL) {
		echo ("can't find");
		pfinish ();
	}

	echo (sprintf ("<a href='index.php'>Back to list</a> | ", h($t)));
	$t = sprintf ("edit.php?id=%d", $id);
	echo (sprintf ("<a href='%s'>Edit</a> | ", h($t)));
	$t = sprintf ("edit.php?id=%d&delete=1", $id);
	echo (sprintf ("<a href='%s'>Delete</a>", h($t)));
	echo ("<div style='padding:.5em'></div>\n");

	$rows = "";

	foreach ($cols as $col) {
		$val = $r->$col;
		$rows .= "<tr>\n";
		$rows .= "<th>$col</th><td>$val</td>\n";
		$rows .= "</tr>\n";
	}

	$rows .= "<tr>";
	$rows .= "<th>bandwidth</th>";
	$rows .= "<td>";
	if ($r->bandwidth == "high") {
		$rows .= sprintf ("<a href='details.php?bandwidth=high&id=%d'"
				  ." class='selected'>High</a> | ", $id);
		$rows .= sprintf ("<a href='details.php?bandwidth=low&id=%d'>"
				  ."Low</a>", $id);
	} else {
		$rows .= sprintf ("<a href='details.php?bandwidth=high&id=%d'>"
				  ."High</a> | ", $id);
		$rows .= sprintf ("<a href='details.php?bandwidth=low&id=%d'"
				  ." class='selected'>Low</a>", $id);
	}
	$rows .= "</td>";
	$rows .= "</tr>";

} else {
	echo ("can't find\n");
	pfinish ();
}

echo ("<table class='twocol'>\n");

echo ($rows);

echo ("</table>\n");

pfinish ();

?>
