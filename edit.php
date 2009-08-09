<?php

require ("common.php");

pstart ();

$id = 0 + @$_REQUEST['id'];
$edit = 0 + @$_REQUEST['edit'];
$delete = 0 + @$_REQUEST['delete'];
$bandwidth = @$_REQUEST['bandwidth'];

function mkrow ($varname, $val) {
	return (sprintf ("<input type='text' size='40'"
			 ."name='%s' value='%s' />\n",
			 h($varname), h($val)));
}

$arg = array ();

foreach ($cols as $col) {
	$arg[$col] = @$_REQUEST[$col];
}

echo ("<div style='padding-top:1em'></div>\n");

if ($id && $delete != 1 && $delete != 2) {
	$stmt = sprintf ("select name, ip, port, switch"
			 ." from comps where id=%d", $id);
	$q = query ($stmt);

	if (($r = fetch ($q)) == NULL) {
		echo ("can't find\n");
		pfinish ();
	}

	echo (sprintf ("<a href='index.php'>Back to list</a> | "));
	echo (sprintf ("<a href='details.php?id=%d'>Details</a> | ", $id));
	echo (sprintf ("<a href='edit.php?id=%d&amp;delete=1'>Delete</a>", $id));
	echo ("<div style='padding:.5em'></div>\n");

	echo ("<form action='edit.php'>\n");
	echo (sprintf ("<input type='hidden' name='id' value='%d' />\n",
		       $id));
	echo ("<input type='hidden' name='edit' value='2' />\n");
	echo (sprintf ("<input type='hidden' name='display' value='%d' />\n",
		       $display));

	$rows = "";

	foreach ($cols as $col) {
		$val = $r->$col;
		$rows .= "<tr>\n";
		$t = sprintf ("<th>%s</th><td>%s</td>\n",
			      $col, mkrow ($col, $val));
		$rows .= $t;
		$rows .= "</tr>\n";
	}
} else if ($delete != 1 && $delete != 2) {
	echo (sprintf ("<a href='index.php'>Back to list</a>"));
	echo ("<div style='padding:.5em'></div>\n");

	echo ("<form action='edit.php'>\n");
	echo (sprintf ("<input type='hidden' name='id' value='%d' />\n",
		       $id));
	echo ("<input type='hidden' name='edit' value='2' />\n");

	$rows = "";
	$val = "";

	foreach ($cols as $col) {
		$rows .= "<tr>\n";
		$t = sprintf ("<th>%s</th><td>%s</td>\n",
			      $col, mkrow ($col, $val));
		$rows .= $t;
		$rows .= "</tr>\n";
	}	
}

if ($delete == 1) {
	$stmt = sprintf ("select name, ip, port, switch"
			 ." from comps where id=%d", $id);
	$q = query ($stmt);

	if (($r = fetch ($q)) == NULL) {
		echo ("can't find\n");
		pfinish ();
	}

	echo ("<form action='edit.php'>\n");
	echo ("<input type='hidden' name='delete' value='2' />\n");
	echo (sprintf ("<input type='hidden' name='id' value='%d' />\n", $id));
	echo (sprintf ("Are you sure you want to delete '%s'?"
		       ." <input type='submit' value='delete' />\n",
		       h($r->name)));

	echo ("</form>\n"); 
	pfinish ();
}

if ($delete == 2) {
	$stmt = sprintf ("select name from comps where id=%d", $id);
	$q = query ($stmt);

	if (($r = fetch ($q)) == NULL) {
		echo ("can't find\n");
		pfinish ();
	}

	query ("delete from comps where id = ?", $id);
	redirect ("index.php");
}

if ($edit == 2) {
	if ($id == 0) {
		$q = query ("select nextval('seq') as seq");
		$r = fetch ($q);
		$id = 0 + $r->seq;

		$stmt = sprintf ("insert into comps (id) values (%d)", $id);
		query ($stmt);
	}

	$t = array ();

	foreach ($cols as $col) {
		if ($arg[$col] == null) {
			$t[] = sprintf ("%s = null", $col);
		} else {
			$t[] = sprintf ("%s = '%s'", $col, h($arg[$col]));
		}
	}

	$t[] = sprintf ("bandwidth = 'high'");

	$stmt = sprintf ("update comps set %s where id = %d",
			 join (",", $t), $id);

	query ($stmt);

	$t = sprintf ("edit.php?id=%d", $id);

	do_commit ();

	redirect ($t);
}

echo ("<table class='twocol'>\n");

echo ($rows);

echo ("<tr><th></th><td><input type='submit' value='Save' /></td></tr>\n");

echo ("</table>\n");

echo ("</form>\n");

pfinish ();

?>
