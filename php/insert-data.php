<?php
include 'config.php';
include 'SINAException.php';
include 'access-bbdd.php';
include 'get-functions.php';
include 'arquivos-bbdd.php';

$dato = $_POST['json'];
$issue_type = $_POST['issue_type'];
debug($issue_type);
$filename = (isset($_POST['file']) != false) ? $_POST['file'] : NULL;
$accessbbdd = new AccessBBDD($connectionstring);
$connected = $accessbbdd->connect();
$connected = (TRUE) ? debug("CONNECTED!!") : debug("!CONNECTED!!") ;
$datos = json_decode($dato, true);

try {		
	$getfeature = new GetFeatures($connectionstring);
	$getfeature->connect();

	$issue_table = "";
	$id_issue = -1;
	
	$issue_table = $getfeature->getIssueTableName($issue_type);
	$id_issue = $accessbbdd->insert($datos, $getfeature->getIssueTypeNum($issue_type));

	$feature = $getfeature->getIssueById($id_issue, $issue_table);
	$result = array( 'result' => 'success', 'feature' => $feature);

	if ($filename != false) {
		# filename has its indidenciaID
		$filename = $id_issue.'-'.$filename;
		$filepath = "'" . $file_www_folder . '/' . $filename . "'";
		
		$arquivo = new Arquivo($connectionstring);
		$arquivo->setRuta($filepath);
		$arquivo->connect();
		$arquivo->insertArquivo($id_issue);
		$arquivo->closeconnection();
	}
	echo json_encode($result);
} catch (SINAException $ex) {
	echo json_encode(array('error' => $ex.getMessage()));
}
?>