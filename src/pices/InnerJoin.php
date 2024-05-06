<?php
namespace src\pices;

class InnerJoin{
	public ?string $nomeJoin = "";
	public ?bool $proximo = false;
	private ?string $tabela2 = "";

	public function addTabela2($tabela2){
		$this->tabela2 = $tabela2;
	}
	public function addNomeJoin($nomeJoin){
		$this->nomeJoin = $nomeJoin;
	}
	public function getNomeJoin(){
		return $this->nomeJoin;
	}
	public function getTabela2(){
		return $this->tabela2;
	}
}