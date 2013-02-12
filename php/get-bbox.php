<?php
include 'config.php';
include 'SINAException.php';
include 'access-bbdd.php';
include 'get-functions.php';

##TODO It must have a "AMBITO" table with the BBOX
$table = '"carto"."'.$_GET['table'].'"';
$column = $_GET['column'];
$code = $_GET['code'];

// $table = '"carto"."provincia"';
// $column = 'cdprov';
// $code = '15';

$getfeatures = new GetFeatures($connectionstring);
$connected = $getfeatures->connect();

try {
	#$feat = $getfeatures->search($dato);
	$bbox = $getfeatures->getBBox($table, $column, $code);
	echo json_encode($bbox);
} catch (SINAException $ex) {
	echo $ex.getMessage();
}
?>
