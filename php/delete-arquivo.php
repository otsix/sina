<?php
include 'config.php';
include 'SINAException.php';
include 'access-bbdd.php';
include 'arquivos-bbdd.php';


$id_issue = $_POST['id_issue'];
$arquivobbdd = new Arquivo($connectionstring);

$connected = $arquivobbdd->connect();
$connected = (TRUE) ? debug("CONNECTED!!") : debug("!CONNECTED!!") ;

$id_arquivo = $arquivobbdd->deleteArquivo($id_issue);
if ($id_arquivo == -1)
	echo json_encode(array('success' => 'no_file'));
else 
	echo json_encode(array('success' => $id_arquivo));
?>