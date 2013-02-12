<?php

include ('config.php');

// connection to database goes here
$dbconn = pg_connect("host=$host dbname=$db_name user=$username password=$password") or die('Could not connect: ' . pg_last_error());
pg_set_client_encoding($dbconn, "utf-8");

//Return all entities
session_start();
//$query = "SELECT id1,cod_ine9 as cod_ine, nome10, pode10, st_asgeojson(the_geom) as geom " . "FROM $tbl_issues WHERE cod_ine9 LIKE '" . $_SESSION['codine'] . "%' ORDER BY cod_ine9";
  //"FROM $tbl_issues WHERE cod_ine9 LIKE '32010%'";
  //echo $query;

$issue_tbl = 'incidencias';
if ($_GET['issue_type']==1){
	$issue_tbl = 'incidencias_seca';
} else if ($_GET['issue_type']==2){
	$issue_tbl = 'incidencias_inundacion';
}

if ($_SESSION['ambito_code'] != 0) {
	$query = "SELECT *,  st_asgeojson(the_geom) as geom FROM ".$issue_tbl." WHERE cd_conc::text LIKE '" . 
				$_SESSION['ambito_code'] . "%' AND id_type=" . $_GET['issue_type']. 
				" ORDER BY id_issue";
} else {
	// Toda Galicia
	$query = "SELECT *,  st_asgeojson(the_geom) as geom FROM ".$issue_tbl.
			 " AND id_type=" . $_GET['issue_type']. " ORDER BY id_issue";
}
				
debug($query);

$result = pg_query($dbconn, $query);

//echo 'NUM FEATS: '. pg_num_rows($result) . "\n";

//Prepare JSON data

$features = array();
while ($row = pg_fetch_array($result)) {
  //Read all columns
  $cols = array_keys($row);
  $cols_length = count($cols);
  $properties = '';
  for ($i = 1; $i < $cols_length - 1; $i=$i+2){
    $k = $cols[$i];
    //TODO remove 'geom' here
    $properties = $properties . '"'. $k .'":"' . $row[$k] . '",';
  };
  $properties = substr($properties, 0, -1);

  $feature = '{ "type": "Feature", "id": ' . $row['id_issue'] . ', "properties": { '.$properties.' }, "geometry": ' . $row['geom'] . ' },';
  array_push($features, $feature);
}

//echo 'NUM FEATS: '. count($features) . "\n";

//Write JSON data

echo '{"type": "FeatureCollection","features": [';
$features_length = count($features);
for ($i = 0; $i < $features_length - 1; $i++) {
	echo $features[$i];
}
if ($features_length != 0) {
	echo substr($features[$features_length - 1], 0, -1);
}
echo ']}';
?>

