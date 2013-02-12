<?php
include 'config.php';
include 'SINAException.php';
include 'access-bbdd.php';
include 'get-functions.php';

$getfeatures = new GetFeatures($connectionstring);
$connected = $getfeatures->connect();
$connected = (TRUE) ? debug("Connected...") : debug("ERROR: NOT CONNECTED!!") ;

if (isset($_GET['cdprov'])){
	$cdprov = $_GET['cdprov'];
}

if (isset($_GET['cdparro'])){
	$cdparro = $_GET['cdparro'];
}

if (isset($_GET['cdconc'])){
	$cdconc = $_GET['cdconc'];
}



try {
	if (isset($cdprov)){
		$list = $getfeatures->getConcellosList($cdprov);		
	} else {
		if (isset($cdconc)){
			$list = $getfeatures->getParroquiasList($cdconc);
		} else {
			if (isset($cdparro)){
				$list = $getfeatures->getEntidadesList($cdparro);
			} else {
				$list = $getfeatures->getProvinciaList();
			}
		}
	}
	echo json_encode($list);
} catch (SINAException $ex) {
	echo $ex.getMessage();
}
?>

