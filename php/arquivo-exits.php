<?php
include 'config.php';
include 'SINAException.php';
include 'access-bbdd.php';
include 'arquivos-bbdd.php';


$id_issue = $_POST['id_issue'];
$arquivobbdd = new Arquivo($connectionstring);

$connected = $arquivobbdd->connect();
$connected = (TRUE) ? debug("CONNECTED!!") : debug("!CONNECTED!!") ;

$arquivo = $arquivobbdd->getArquivo($id_issue);
if ($arquivo == -1)
	echo json_encode(array('error' => 'error'));
else 
	echo json_encode($arquivo);

?>