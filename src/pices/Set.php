<?php
namespace src\pices;

class Set{
	public ?array $value = [];
	public ?string $operador = null;
	public ?string $clause = "";

	public function addValue($key,$value){
		$this->value[$key] = $value;
	}
	public function addOperador($operador){
		$this->operador = $operador;
	}
	public function addClause($clause){
		$espacos = $clause." ";
		$this->clause = $espacos;
	}
	public function getValue(){
		return $this->value;
	}
	public function getOperador(){
		return $this->operador;
	}
	public function getClause(){
		return $this->clause;
	}
}