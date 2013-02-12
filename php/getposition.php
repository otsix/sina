<?php
include 'config.php';
include 'SINAException.php';
include 'access-bbdd.php';
include 'get-functions.php';

$lat = $_POST['lat'];
$lon = $_POST['lon'];
debug($lat . "  " . $lon);
$getfeatures = new GetFeatures($connectionstring);
$connected = $getfeatures->connect();
$connected = (TRUE) ? debug("CONNECTED!!") : debug("!CONNECTED!!") ;
$provincia = $getfeatures->getProvinciaByPosition($lat, $lon);
$concello = $getfeatures->getConcelloByPosition($lat, $lon);

// Comprobacion de si el usuario puede insertar en ese lugar
$ambito_code = strval($_SESSION['ambito_code']);
// equivalente a startWith()
$is_permited = strncmp((string)$concello['cdconc'], (string)$ambito_code, strlen($ambito_code));

debug("ambito_code: ".strval($ambito_code));
debug("concello: ".strval($concello['cdconc']));
debug("comparacion: ".$is_permited);
if ($ambito_code == 0){
	//Usuario es para toda galicia
	//No hacer nada
} else if ($is_permited!=0){
	echo "ERRO: Área non permitida para o usuario actual.";
	return;
} 

$parroquia = $getfeatures->getParroquiaByPosition($lat, $lon);
$entidades = $getfeatures->getBufferEntidades($lat, $lon);
$position = array('concello' => $concello, 'parroquia' => $parroquia, 'entidades' => $entidades, 'provincia' => $provincia);
echo json_encode($position);
?>