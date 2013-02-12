<?php
include 'config.php';
include 'SINAException.php';
include 'access-bbdd.php';

$id = $_POST['id'];
$accessbbdd = new AccessBBDD($connectionstring);
$connected = $accessbbdd->connect();
$connected = (TRUE) ? debug("CONNECTED!!") : debug("!CONNECTED!!") ;
try {
	$accessbbdd->remove($id);
	echo 'Insert OK!';
} catch (SINAException $ex) {
	echo $ex.getMessage();
}
?>