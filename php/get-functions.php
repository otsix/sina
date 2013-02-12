<?php

class GetFeatures extends AccessBBDD {
	
	function __construct($connectionstring) {
		parent::__construct($connectionstring);
	}
	
    private function convertFeaturesToJSON($result) {
		
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
		return $features;
	}
	
	public function getIssueTableName($issue_type){
		$issue_table = '';
		if ($issue_type == "seca"){
			// "seca"==1
			//TODO debería llamarse "indicencias_seca"
			$issue_table = "incidencias_seca";
		} elseif ($issue_type == "inund"){
			// "inund"==2
			$issue_table = "incidencias_inundacion";
		};
		return $issue_table;
	}
	
	public function getIssueTypeNum($issue_type){
		$issue_type_num = -1;
		if ($issue_type == "seca"){
			$issue_type_num = 1;
		} elseif ($issue_type == "inund"){
			$issue_type_num = 2;
		};
		return $issue_type_num;
	} 
	
	
	
	public function getAllIssues() {
		$query = "SELECT *,  st_asgeojson(the_geom) as geom FROM incidencias";
		$result = pg_query($this -> conn, $query);
		
		//Prepare JSON data
		$features = $this -> convertFeaturesToJSON($result);
		 
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
	}

	public function getIssueById($id, $incidencias_table) {
		$sqlquery = "SELECT *, st_asgeojson(the_geom) as geom from ". $incidencias_table . 
					" where id_issue = " . $id;
		debug($sqlquery);
		$result = pg_query($this -> conn, $sqlquery);
		
		if (pg_num_rows($result) != 0) {
			$features = $this -> convertFeaturesToJSON($result);
		}
		return substr($features[0], 0, -1);
	}
	
	public function getConcelloById($id) {
		$sqlquery = "SELECT * from carto.concello c, public.incidencias i where i.id_issue=" . 
					$id ." and st_contains(c.the_geom, i.the_geom)";
		$result = pg_query($this -> conn, $sqlquery);
		
		if (pg_num_rows($result) != 0) {
			$row = pg_fetch_array($result);
			$nome = $row['nome']; 
		}	

		return $nome;
	}
	
	public function getParroquiaById($id) {
		$sqlquery = "SELECT * from carto.parroquia p, public.incidencias i where i.id_issue=" . $id ." and st_contains(p.the_geom, i.the_geom)";
		$result = pg_query($this -> conn, $sqlquery);
		
		if (pg_num_rows($result) != 0) {
			$row = pg_fetch_array($result);
			$nome = $row['nome']; 
		}

		return $nome;
	}	

	public function getConcelloByPosition($x, $y) {
		$sqlquery = "SELECT nome, cdconc from carto.concello p where st_contains(p.the_geom, 'SRID=23029;POINT(" . $x . " " . $y . ")')";
		debug($sqlquery);
		$result = pg_query($this -> conn, $sqlquery);
		$concello = '';
		if (pg_num_rows($result) != 0) {
			$row = pg_fetch_array($result);
			$concello = array('cdconc' => $row['cdconc'], 'nome' => $row['nome']); 
		}

		return $concello;
	}
	
	public function getParroquiaByPosition($x, $y) {
		$sqlquery = "SELECT nome, codparro from carto.parroquia p where st_contains(p.the_geom, 'SRID=23029;POINT(" . $x . " " . $y . ")')";
		debug($sqlquery);
		$result = pg_query($this -> conn, $sqlquery);
		$parroquia = '';
		if (pg_num_rows($result) != 0) {
			$row = pg_fetch_array($result);
			$parroquia = array('codparro' => $row['codparro'], 'nome' => $row['nome']); 
		}

		return $parroquia;
	}
	
	public function getProvinciaByPosition($x, $y) {
		$sqlquery = "SELECT nome, cdprov from carto.provincia p where st_contains(p.the_geom, 'SRID=23029;POINT(" . $x . " " . $y . ")')";
		debug($sqlquery);
		$result = pg_query($this -> conn, $sqlquery);
		$parroquia = '';
		if (pg_num_rows($result) != 0) {
			$row = pg_fetch_array($result);
			$parroquia = array('cdprov' => $row['cdprov'], 'nome' => $row['nome']); 
		}

		return $parroquia;
	}
	
	public function getBufferEntidades($x, $y) {
		$sqlquery = "select cod_ine9,nomb10 from carto.entidade_singular as e ".
		" where st_contains(st_buffer('SRID=23029;POINT(" . $x . " " . $y . ")', 1500), e.the_geom) ".
		"order by nomb10";
		debug($sqlquery);
		
		$result = pg_query($this -> conn, $sqlquery);
		$entidades = array();
		while ($row = pg_fetch_array($result)) {
			$cols = array_keys($row);
			$cols_length = count($cols);
			$entidad = array();
  			for ($i = 1; $i < $cols_length; $i=$i+2){
				$k = $cols[$i];
    			$entidad[$k] = $row[$k];
  			};
			array_push($entidades, $entidad);
		}
		
		return $entidades;
	}
	
	public function getProvinciaList(){
		$sqlquery = 'SELECT cdprov, nome '.
					'FROM carto.provincia '.
					'ORDER BY cdprov';
		debug($sqlquery);
		$result = pg_query($this -> conn, $sqlquery);
		return pg_fetch_all($result);
	}
	
	public function getConcellosList($cdprov){
		$sqlquery = 'select cdconc, nome from carto.concello '.
		'WHERE cdprov = '. $cdprov .' '.
		'ORDER BY cdconc';
		debug($sqlquery);
		$result = pg_query($this -> conn, $sqlquery);
		return pg_fetch_all($result);
	}

	public function getParroquiasList($cdconc){
		$sqlquery = 'select codparro, nome from carto.parroquia '.
		"WHERE codparro::text LIKE '". $cdconc ."%' ".
		'ORDER BY codparro';
		debug($sqlquery);
		$result = pg_query($this -> conn, $sqlquery);
		return pg_fetch_all($result);
	}
	
	public function getEntidadesList($cdconc){
		$sqlquery = 'select cod_ine9, nomb10 from carto.entidade_singular '.
		"WHERE cod_ine9::text LIKE '". $cdconc . "%' ".
		'ORDER BY nomb10';
		debug($sqlquery);
		$result = pg_query($this -> conn, $sqlquery);
		return pg_fetch_all($result);
	}
	
	
	public function getBBox($tbl, $col, $id){
		$sqlbbox = 'SELECT st_xmax(the_geom) as xmax, st_ymax(the_geom) as ymax, ' . 
		'st_xmin(the_geom) as xmin, st_ymin(the_geom) as ymin ' . 
		'FROM '.$tbl.' WHERE "'.$col.'"=' . $id . ';';
		debug($sqlbbox);
		$result_bbox = pg_query($this->conn, $sqlbbox);
		
		$count_bbox = pg_num_rows($result_bbox);
		if ($count_bbox == 1) {
			$row = pg_fetch_row($result_bbox);
			$xmax = $row[pg_field_num($result_bbox, 'xmax')];
			$ymax = $row[pg_field_num($result_bbox, 'ymax')];
			$xmin = $row[pg_field_num($result_bbox, 'xmin')];
			$ymin = $row[pg_field_num($result_bbox, 'ymin')];
			$bbox = array();
			$bbox['xmax'] = $xmax;
			$bbox['ymax'] = $ymax;
			$bbox['xmin'] = $xmin;
			$bbox['ymin'] = $ymin;
			//$bbox = $xmax . ',' . $ymax . ',' . $xmin . ',' . $ymin;
		}
		return $bbox;
	}
	
}
?>