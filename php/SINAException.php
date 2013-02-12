<?php

class SINAException extends Exception
{
	var $operation;
	var $tabla;
	
    public function __construct($operation, $tabla, $message, $code = 0, Exception $previous = null) {
    	
		$this->operation = $operation;
		$this->tabla = $tabla;
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function getOperation() {
        return $this->operation;
    }
	
	public function getTable() {
        return $this->tabla;
    }
}

?>