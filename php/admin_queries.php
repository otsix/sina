<?php

include ('config.php');

function summarize($dbconn) {

	include ('config.php');

	// Entidades afectadas totales
	$sql = "SELECT gid FROM $tbl_entities WHERE modified_by IS NOT NULL";
	$res = pg_query($dbconn, $sql) or die('Query failed: ' . pg_last_error());
	$num_ent_afectados = pg_num_rows($res);

	// Habitantes afectados totales
	$sql = "SELECT sum(afectados) as total FROM $tbl_entities where modified_by IS NOT NULL";
	$res = pg_query($dbconn, $sql) or die('Query failed: ' . pg_last_error());
	if (pg_num_rows($res) > 0) {
		$row = pg_fetch_row($res);
		$num_hab_afectados = $row[pg_field_num($res, 'total')];
		if (!isset($num_hab_afectados)) {
			$num_hab_afectados = 0;
		};
	}

	// Concellos con datos
	$sql = "SELECT substring(cod_ine from '.....') as codine, count(*) FROM $tbl_entities WHERE modified_by IS NOT NULL GROUP BY codine";
	$res = pg_query($dbconn, $sql) or die('Query failed: ' . pg_last_error());
	$num_concellos = pg_num_rows($res);

	$htable .= '<table cellpadding="0" cellspacing="0" class="' . $class_tag . '">';
	$htable .= '<tr><th> Total Nucleos afectados: </th> <td>' . $num_ent_afectados . '</td></tr>';
	$htable .= '<tr><th> Total Habitantes afectados: </th> <td>' . $num_hab_afectados . '</td></tr>';
	$htable .= '<tr><th> Concellos que enviaron datos: </th> <td>' . $num_concellos . '</td></tr>';
	$htable .= '</table>';
	return $htable;

}

function table2html($result, $class_tag) {

	$htable = '';

	$count = pg_num_rows($result);
	if ($count) {
		$htable .= '<table cellpadding="0" cellspacing="0" class="' . $class_tag . '">';
		// table header
		$htable .= '<tr>';
		for ($idx; $idx < pg_num_fields($result); $idx++) {
			$htable .= '<th>' . pg_field_name($result, $idx) . '</th>';
		}
		$htable .= '</tr>';
		// table data
		while ($row = pg_fetch_row($result)) {
			$htable .= '<tr>';
			foreach ($row as $key => $value) {
				$htable .= '<td>' . $value . '</td>';
			}
			$htable .= '</tr>';
		}
		$htable .= '</table><br />';
	}
	return $htable;
}

$dbconn = pg_connect("host=$host dbname=$db_name user=$username password=$password") or die('Could not connect: ' . pg_last_error());

$query = "summarize_tables";
//$_GET['query'];

//echo "\n ".$sql."\n";

if ($query == "summarize_tables") {
	echo summarize($dbconn);
} else {
	$concello = $_GET['concello'];
	$sql = "SELECT * FROM $tbl_users where concello = '" . $concello . "'";
	$res = pg_query($dbconn, $sql) or die('Query failed: ' . pg_last_error());
	echo table2html($res, "db-table");
}
?>

