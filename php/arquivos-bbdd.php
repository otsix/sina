<?php
/*
 * 
 */

class Arquivo extends AccessBBDD
{
	var $ruta;
	
	function __construct($connectionstring) 
	{
		parent::__construct($connectionstring);
	}

	public function setRuta($ruta)
	{
		$this->ruta = $ruta;
	}
	
	public function insertArquivo($id_issue) 
	{
		$sql = "INSERT into arquivo (id_issue, ruta) values (" . $id_issue . ", " . $this->ruta . ") returning id";
		try {
			$result = $this -> execquery($sql);
			$id_arquivo = $result['id'];
			
		} catch (Exception $e) {
			throw new SINAException('insert', 'arquivo', 'Error al insertar arquivo con issue id=' . $id);
		};		
	}
	
	public function updateArquivo($id_issue) 
	{
		$sql = "UPDATE arquivo ruta =" . $this->ruta . "where id_issue = " . $id_issue . " returning id";
		try {
			$result = $this -> execquery($sql);
			$id_arquivo = $result['id'];
			
		} catch (Exception $e) {
			throw new SINAException('update', 'arquivo', 'Error al actualizar arquivo con issue id=' . $id);
		};	
	}
	
	public function deleteArquivo($id_issue) 
	{
		$sql = "DELETE from arquivo where id_issue = " . $id_issue . " returning id";
		try {
			$result = $this -> execquery($sql);
			$id_arquivo = $result['id'];
			if ($id_arquivo == false)
				return -1;
			else
				return $id_arquivo;
			
		} catch (Exception $e) {
			throw new SINAException('delete', 'arquivo', 'Error al eliminar arquivo con issue id=' . $id);
		};	
	}
	
	public function getArquivo($id_issue)
	{
		$sql = "SELECT * from arquivo where id_issue = " . $id_issue;
		try {
			$result = pg_query($this->conn, $sql);
			
			if (pg_num_rows($result) != 0) {
				$row = pg_fetch_array($result);
				return array('id' =>  $row['id'], 'ruta' => $row['ruta']);
			}
			return -1;
						
		} catch (Exception $e) {
			throw new SINAException('select', 'arquivo', 'Error al seleccionar arquivo con issue id=' . $id);
		};			
	}
	
}

?>