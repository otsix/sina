<?php
/**
 *
 */

session_start();
 
class AccessBBDD {

	protected $connectionstring;
	protected $conn;

	function __construct($connectionstring) {
		$this -> connectionstring = $connectionstring;
	}

	public function connect() {
		$this -> conn = pg_connect($this -> connectionstring);
		pg_set_client_encoding($this -> conn, "utf-8");
		if ($this -> conn != NULL) {
			return true;
		} else {
			return false;
		}
	}
	
	private function extractcoordinates($valuesarray) {
		$coordinates = "'SRID=23029;POINT (" . $valuesarray['coordinates']['coordx_issue'] . " " . $valuesarray['coordinates']['coordy_issue'] . ")'";
		return $coordinates;			
	}

	private function extractissuevalues($valuesarray) {
		$issuearray = array();
		array_push($issuearray, "issue", implode(",", array_keys($valuesarray['issue'])), implode(",", array_values($valuesarray['issue'])));

		return $issuearray;
	}
	
	public function manualupdatemodifiedby($id_issue) {
		# UPDATE MANUALLY MODIFIED_BY and M_TIMESTAMP
		$sqlstring = 'UPDATE issue set modified_by=' . $_SESSION['user_id']. ' where id_issue=' . $id_issue;
		try {
			$this -> execquery($sqlstring);
		} catch (Exception $e) {
			debug('ERROR:' . $sqlstring);
		};
		$sqlstring = 'UPDATE issue set m_timestamp=NOW() where id_issue=' . $id_issue;
		try {
			$this -> execquery($sqlstring);
		} catch (Exception $e) {
			debug('ERROR:' . $sqlstring);
		};
		return 0;	
	}

	public function insert($valuesarray, $issue_type) {
		$coordinates = $this->extractcoordinates($valuesarray);
		$issuearray = $this -> extractissuevalues($valuesarray);
		// seca id_type=1;
		$sqlstring = "INSERT into " . $issuearray[0] . " ( the_geom, id_type, " . $issuearray[1] 
					. ") VALUES (" . $coordinates . ", ".$issue_type.", " 
					. $issuearray[2] . ") RETURNING id_issue";
		debug($sqlstring);
		try {
			$result = $this -> execquery($sqlstring);
		} catch (Exception $e) {
			throw new SINAException('insert', 'issue', 'Error al insertar la issue con id=' . $id);
		};
		$id_issue = $result['id_issue'];

		if ($result != 0) {
			foreach ($valuesarray as $key => $value) {
				$valuesarray[$key] = $this->extractBreakLines($valuesarray[$key]);
				if ($key != 'issue' && $key != 'coordinates') {
					$sqlstring = "INSERT into " . $key . " (id_issue, " . implode(",", array_keys($valuesarray[$key])) . 
					") VALUES (" . $id_issue . "," . implode(",", array_values($valuesarray[$key])) . ")";
					debug($sqlstring);
					try {
						$result = $this -> execquery($sqlstring);
					} catch (Exception $e) {
						throw new SINAException('insert', 'issue', 'Error al insertar la '. $tablaupdate . ' con id_issue=' . $id);
					};
				}
			}
		
			# UPDATE MANUALLY CREATED_BY (C_timestamp is set by a trigger)
			$sqlstring = 'UPDATE issue set created_by=' . $_SESSION['user_id']. ' where id_issue=' . $id_issue;
			try {
				$this -> execquery($sqlstring);
			} catch (Exception $e) {
				debug('ERROR:' . $sqlstring);
			};	
		
		}		
		
		return $id_issue;
	}

	private function extractBreakLines($arr) {
		$arrvalores = array();
		foreach ($arr as $clave => $valor) {
			$arrvalores[$clave] = preg_replace("[\n|\r|\n\r]", '@#!', $valor);
		}
		return $arrvalores;
	}
	
	public function remove($id)
	{
		$sqlstring = "UPDATE issue set id_status=0, active=false where id_issue = " . $id;
		debug($sqlstring);
		try {
			$result = $this -> execquery($sqlstring);
			$this -> manualupdatemodifiedby($id);
		} catch (Exception $e) {
			throw new SINAException('delete', 'issue', 'Error al eliminar la issue con id=' . $id);
		};
	}
	
	public function update($id, $valuearray)
	{
		$issueupdate = '';
		foreach ($valuearray['issue'] as $key => $value) {
			$issueupdate = $issueupdate . $key . '=' . $value . ',';
		}
		$issueupdate = substr($issueupdate, 0, -1);
		$sqlstring = 'UPDATE issue set ' .  $issueupdate . ' where id_issue=' . $id . ' RETURNING id_issue';
		debug($sqlstring);
		try {
			$result = $this -> execquery($sqlstring);
			$id_issue = $result['id_issue'];
		} catch (Exception $e) {
			throw new SINAException('update', 'issue', 'Error al actualizar la issue con id=' . $id);
		};
		

		if ($result != 0) {
			foreach ($valuearray as $tabla => $datos) {
				if ($tabla != 'issue' && $tabla != 'coordinates') {
					$tablaupdate = '';
					foreach ($valuearray[$tabla] as $key => $value) {
						$tablaupdate = $tablaupdate . $key . '=' . preg_replace("[\n|\r|\n\r]", '@#!', $value) . ',';
					}
					$tablaupdate = substr($tablaupdate, 0, -1);
					$sqlstring = 'UPDATE ' . $tabla . ' set ' . $tablaupdate . ' where id_issue=' . $id;
					debug($sqlstring);
					try {
						$this -> execquery($sqlstring);
					} catch (Exception $e) {
						throw new SINAException('update', $tablaupdate, 'Error al actualizar la '. $tablaupdate . ' con id_issue=' . $id);
					};				
				}
			}
			$this -> manualupdatemodifiedby($id);
		}
		return $id_issue;
	}


	public function execquery($sql) {
		$result = pg_query($this -> conn, $sql);
		$resultados = pg_fetch_assoc($result);

		return $resultados;
	}
	
	public function closeconnection() {
		return pg_close($this -> conn);
	}

}
?>