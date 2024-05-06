<?php
namespace src\pices;

class Insert{
	public ?array $value = [];
	public ?bool $proximo = false;
	public ?string $clause = "";

	public function addValue($key,$value){
		$this->value[$key] = $value;
	}
	public function addClause($clause){
		$espacos = $clause." ";
		$this->clause = $espacos;
	}
	public function getValue(){
		return $this->value;
	}
	public function getClause(){
		return $this->clause;
	}
}