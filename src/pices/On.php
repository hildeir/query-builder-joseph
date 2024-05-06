<?php
namespace src\pices;

class On{
	private ?array $value = [];
	private ?string $operador = null;
	public ?bool $proximo = false;
	private ?string $clause = "";
	private ?string $nameClause = "";

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
	public function getNameClause(){
		return $this->nameClause;
	}
	public function setNameClause($name){
		$this->nameClause = $name;
	}
}