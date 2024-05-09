<?php
namespace src;

use \src\Database;
use \src\pices\Where;
use \src\pices\Andd;
use \src\pices\On;
use \src\pices\Insert;
use \src\pices\InnerJoin;
use \src\pices\LeftJoin;
use \src\pices\Set;

class QueryBuilder extends Database{ 
	
	private ?string $tabela; 
	private ?string $tabela2;
	private ?string $column;
	private $objWhere;
	private $objAnd;
	private $objInsert;
	private $objSet;
	private $acumuladorJoin = [];
	private $acumuladorOn = [];

	function __construct($column,$nomeMetodo) {

		$this->getClasse();
		if($nomeMetodo == "select"){
			$this->objWhere = new Where();
			$this->objAnd = new Andd();
			$this->selectOrdem($column);
		}
		if($nomeMetodo == "insert"){
			$this->objInsert = new Insert();
			$this->insertBuild($column);
		}
		if($nomeMetodo == "update"){
			$this->objSet = new Set();
			$this->objWhere = new Where();
		}
		
	}
	private function constructSet($column){
		$this->setBuild($column);
	}
	private static function pdo(){
		return parent::getPdo();
	}
	public static function lastId(){
		$pdo = self::pdo();
		$id = $pdo->lastInsertId();
		return $id;
	}
	private function getClasse(){
		$classname = get_called_class();
		
        if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
            $classname = $matches[1];
            $this->tabela = $classname;
        }
	}
	private static function insertIsEmpty($column){
		if($column == []){
			echo "nenhum valor foi encontrado no parâmetro do insert";
			
		}
	}
	
	private static function setIsEmpty($column){
		if($column == []){
			echo "nenhum valor foi encontrado no parâmetro do set";
			
		}
	}
	private function selectOrdem(array $column) {
		if ($column == []) {
			$this->column = "*";

		} else {
			$select_string = "";
			for ($i = 0; $i < count($column); $i++) {
				if($i == 0){
				$select_string .= $column[$i];	
				}else{
					$select_string .= ",".$column[$i];
				}
				
			}
			$this->column = $select_string;
		}
		
	}
	
	public static function select(array $column = []) {
		$nomeMetodo = "select";
		return new static($column,$nomeMetodo);
	}
	public static function insert(array $column = []){
		self::insertIsEmpty($column);
		$nomeMetodo = "insert";
		return new static($column,$nomeMetodo);
	}
	public static function update(){
		$nomeMetodo = "update";
		$column = "";
		return new static($column,$nomeMetodo);
	}
	public function set(array $column = []){
		self::setIsEmpty($column);
		$this->constructSet($column);
		return $this;
	}
	public function where(string $arg1 = "", string $arg2 = "", string $arg3 = "") {
		$nomeClause = "where";
		$this->clauseBuild($this->objWhere,$arg1,$arg2,$arg3,$nomeClause);
		return $this;
	}
	public function and(string $arg1 = "", string $arg2 = "", string $arg3 = ""){
		$nomeClause = "and";
		$this->clauseBuild($this->objAnd,$arg1,$arg2,$arg3,$nomeClause);
		return $this;
	}
	public function on(string $arg1 = "", string $arg2 = ""){
		
		$nomeClause = "on";
		$objOn = new On();
		$this->clauseBuildOn($objOn,$arg1,$arg2,$nomeClause);
		$this->acumuladorOn($objOn);
		
		return $this;
	}
	public function innerJoin($tabela2){
		
		$innerJoin = new InnerJoin();
		$nomeJoin = "inner join";
		$innerJoin->proximo = true;
		$innerJoin->addTabela2($tabela2);
		$innerJoin->addNomeJoin($nomeJoin);

		$this->acumuladorJoin($innerJoin);
		return $this;
	}
	public function leftJoin($tabela2){
		
		$leftJoin = new LeftJoin();
		$nomeJoin = "left join";
		$leftJoin->proximo = true;
		$leftJoin->addTabela2($tabela2);
		$leftJoin->addNomeJoin($nomeJoin);

		$this->acumuladorJoin($leftJoin);
		return $this;
	}
	
	private function acumuladorJoin($objJoin){
		
		$this->acumuladorJoin[] = $objJoin; 
	}
	private function acumuladorOn($objOn){
		
		$this->acumuladorOn[] = $objOn;
	}
	private function getacumuladorJoin(){
		return $this->acumuladorJoin;
	}
	private function getAcumuladorOn(){
		return $this->acumuladorOn;
	}
	private function insertBuild($column){
		$this->objInsert->addClause("insert into");
		
		foreach($column as $key => $value){
			$this->objInsert->addValue($key,$value);
		}
		
	}
	
	private function setBuild($column){
		$this->objSet->addClause("set");
		foreach($column as $key => $value){
			$this->objSet->addValue($key,$value);
		}
		$this->objSet->addOperador("=");
	}
	
	private function clauseBuildOn($objClause,$arg1,$arg2,$nomeClause){
		$objClause->setNameClause($nomeClause);
		$objClause->proximo = true;
		$objClause->addClause($nomeClause);

		if ($arg2 == "" && $arg1 == "") {
			echo "falta parâmetros";
			exit();
		}
		//dois argumentos
		if($arg1 != "" && $arg2 != "") {
			$args = ["column"=>$arg1,"value"=>$arg2];

			foreach ($args as $key=>$value){
				$objClause->addValue($key,$value);
			}
			$objClause->addOperador(" = ");
			
		}
	
	}
	private function clauseBuild($objClause,$arg1,$arg2,$arg3,$nomeClause){
		$objClause->proximo = true;
		$objClause->addClause($nomeClause);

		if ($arg3 == "" && $arg2 == "" && $arg1 == "") {
			echo "falta parâmetros";
			exit();
		}
		//dois argumentos
		if($arg1 != "" && $arg2 != "") {
			$args = ["column"=>$arg1,"value"=>$arg2];

			foreach ($args as $key=>$value){
				$objClause->addValue($key,$value);
			}
			$objClause->addOperador("=");
			
		}//todos os argumentos que são 3
		else if($arg1 != "" && $arg2 != "" && $arg3 != ""){
			$op = [">","<",">=","<=","<>"];
			$notsearch = false;
			for ($i = 0; $i < count($op); $i++) {
				if ($op[$i] == $arg2) {
					$notsearch = true;
					break;
				} else {
					$notsearch = false;
					continue;
				}
			}
			if ($notsearch == false) {
				echo "erro! operador nao encontrado";
				exit();
			} else {
				$args = ["column" => $arg1,"value" => $arg3];
				foreach($args as $key => $value){
					$objClause->addValue($key,$value);
				}
				
				$objClause->addOperador($arg2);
				
			}

		}
	}
	public function get() {
		
		$tabela = $this->tabela;
		$select = $this->column;
		
		$selectDefinido = isset($select);
		$temInnerJoin = $this->getacumuladorJoin();
		$temOn = $this->getAcumuladorOn();
		/** sobre a variavel $objClause **/
		// os objetos atribuido na variavel $objClause é 
		// as clauses' seguida na ordem conforme a sintaxe do SQL do mysql Ex: select... where id = 1 and etc = etc
		// primeiro vem a clause where depois o and
		/******** fim  ********/
		/////////// WHERE //////////////
		//quando houver o select e o where
		if($selectDefinido == true && $this->objWhere->proximo == true &&
		$this->objAnd->proximo == false && $temInnerJoin == []){
	
			$objClause = [$this->objWhere];
			$sql = $this->executaSqlSelect($select,$tabela,$objClause);
			return $sql;
			
		} ////// SELECT ////////////
		//////// quando haver apenas o select //////////////
		else if($selectDefinido == true && $this->objWhere->proximo == false &&
		 $this->objAnd->proximo == false && $temInnerJoin == []){
			
			$sql = $this->executaSqlSelect($select,$tabela);		
			return $sql;
		} /////////// AND //////////
		////////////// quando haver select, where e and
		else if($selectDefinido == true && $this->objWhere->proximo == true &&
		 $this->objAnd->proximo == true && $temInnerJoin == []){
			
			$objClause = [$this->objWhere,$this->objAnd]; 
			$sql = $this->executaSqlSelect($select,$tabela,$objClause);
			return $sql;

		}
		///////// quando haver select, inner join, on , where
		else if($selectDefinido == true && $this->objWhere->proximo == true && 
				$temInnerJoin != [] && $this->objAnd->proximo == false &&
				 $temOn != [] && $this->objAnd->proximo == false){
					
			$objClauseOn = $temOn;
			$objClauseWhere = $this->objWhere;
			$objClauses = [$objClauseOn, "where" => $objClauseWhere];
			$sql = $this->executaJoinSql($select, $temInnerJoin, $objClauses,$tabela);
			return $sql;
		}
		///////// quando haver select, inner join, on 
		else if($selectDefinido == true && $this->objWhere->proximo == false && 
				$temInnerJoin != [] && $this->objAnd->proximo == false &&
				 $temOn != [] && $this->objAnd->proximo == false){
					
			$objClauseOn = $temOn;
			$objClauses = [$objClauseOn, "where" => false];
			
			$sql = $this->executaJoinSql($select, $temInnerJoin, $objClauses,$tabela);
			return $sql;
		}
		
	}
	public function executaJoinSql($select, array $objJoin, array $objClauses, $tabela){
		if($objClauses["where"] == false){
			//sintaxe de implementação sem o where:
			//tabela1::select(["tabela1.coluna","tabela2.coluna",etc...])->innerJoin("tabela2")->on("tabela1.coluna","tabela2.coluna")->get()
			// ou 
			//tabela1::select(["tabela1.coluna","tabela2.coluna",etc...])->innerJoin("tabela2")->on("tabela1.coluna","tabela2.coluna")->innerJoin("tabela3")
			//->on("tabela3.coluna","escolher aqui qual tabela usar.coluna")->get()
			$c = 0;
			$sql = "select $select from $tabela";
			
			for($j = 0; $j < count($objJoin); $j++) { 

				$join = $objJoin[$j]->getNomeJoin();
				$joinTabela = $objJoin[$j]->getTabela2();

				$sql .= " $join $joinTabela ";

				while($c < count($objClauses[0])){
					$clause = $objClauses[0][$c]->getClause();
					$values = $objClauses[0][$c]->getValue();
					$column2 = $values["value"];
					$column = $values["column"]; 

					$clause = $column;
					$clause .= $objClauses[0][$c]->getOperador();
					$clause .= $column2;

					$nomeComando = $objClauses[0][$c]->getNameClause();

					$sql .= " $nomeComando $clause ";

					break;
				}
				$c++;
			}
			
			$pdo = self::pdo();
			$query = $pdo->prepare($sql);
			$query->execute();
			$res = $query->fetchAll(\PDO::FETCH_ASSOC);
		
			return $res;
		}else{
			//sintaxe de implementacao com o where
			//tabela1::select(["tabela1.coluna","tabela2.coluna","etc..."])->innerJoin("tabela2")->on("tabela1.coluna","tabela2.coluna")->where("tabela1.coluna",valor)->get()
			$c = 0;
			$sql = "select $select from $tabela";
			for($j = 0; $j < count($objJoin); $j++) { 

				$join = $objJoin[$j]->getNomeJoin();
				$joinTabela = $objJoin[$j]->getTabela2();

				$sql .= " $join $joinTabela ";

				while($c < count($objClauses[0])){
					$clause = $objClauses[0][$c]->getClause();
					$values = $objClauses[0][$c]->getValue();
					$column2 = $values["value"];
					$column = $values["column"]; 

					$clause = $column;
					$clause .= $objClauses[0][$c]->getOperador();
					$clause .= $column2;

					$nomeComando = $objClauses[0][$c]->getNameClause();

					$sql .= " $nomeComando $clause ";

					break;
				}
				$c++;
			}
			
			$clauses = $objClauses["where"]->getClause();
			$values = $objClauses["where"]->getValue();
			$bindValue = ":valor";
			$column = $values["column"];
			
			$clauses .= $column;
			$clauses .= $objClauses["where"]->getOperador();
			$clauses .= $bindValue;

			$sql .= $clauses;
			
			$pdo = self::pdo();
			$query = $pdo->prepare($sql);
			$query->bindValue($bindValue,$values["value"]);
			$query->execute();
			$res = $query->fetchAll(\PDO::FETCH_ASSOC);
	
			return $res;
					
		}
		
	}
	public function executaSqlSelect(?string $select,?string $tabela, ?array $clause = []){
	
		//quando não há clauses
		if($clause == [] && $select != "" && $tabela != ""){
			$sql = "select $select from $tabela";
			
			$pdo = self::pdo();
			$query = $pdo->prepare($sql);
			$query->execute();
			$res = $query->fetchAll();
			
			return $res;
			
		}else{
			//quando há clauses
			$arrayBindValue = [];
			$storeClauses = "";
			foreach($clause as $obj){
				
				//variavel $sintaxe, exemplo: column = :column
				$clauses = $obj->getClause();
				$values = $obj->getValue();
				//caso o valor da coluna foi pasado como vazio entao encerra o programa
				//exemplo: column = ""
				if(isset($values["column"]) == false){
					echo "foi passado valores vazios na execução do select";
					exit;
				}
				$bindValue = ":" . $values["column"];
				$column = $values["column"];
				
				$clauses .= $column;
				$clauses .= $obj->getOperador();
				$clauses .= $bindValue;
				$arrayBindValue[$bindValue] = $values["value"];
				$storeClauses .= $clauses;
			}
			$sql = "select $select from $tabela $storeClauses";
				
			$pdo = self::pdo();
			$query = $pdo->prepare($sql);
			$query->execute($arrayBindValue);				
			$res = $query->fetchAll();

			return $res;
			
		}
	
	}
	/***** execute do insert e do update *******/
	public function execute(){
	
		if($this->objSet != null){
			$this->executeSqlUpdate();

		}else if($this->objInsert != null){
			$return = $this->executeSqlInsert();
			
			return $return;
		}
	}
	private function executeSqlInsert(){
		$tabela = $this->tabela;
		$values = $this->objInsert->getValue();
		
		
		$colunas = "";
		$bindValues = "";
		$arrayBindValues;
		/*** concatena as colunas e os bindvalues e cria um array com os bindValues **/
		foreach($values as $key => $value){
			$colunas .= ",";
			$colunas .= $key;
			$bindValues .= ",";
			$bindValues .= ":" . $key;
			$arrayBindValues[":".$key] = $value;

		}
		/***** fim  *******/
		$colunas = substr($colunas,1); //remove a primeira virgula 
		$bindValues = substr($bindValues,1); //remove a primeira virgula
		
		$sql = "insert into $tabela ($colunas) values ($bindValues)";
		$pdo = self::pdo();
		$query = $pdo->prepare($sql);
		$query->execute($arrayBindValues);
		
	}
	
	private function executeSqlUpdate(){
		/************* Exemplo de Update ********/ 
		//  tabela1::update()->set(["nome" => "fulano","olhos" => "preto"])->where("id",1)->execute();
		$tabela = $this->tabela;
		$where_operacao = "";
		$set = "";
		$bindValueWhere;
		$valueBindValueWhere;
		//se nao for chamada o metodo where() entao encerra o programa
		if($this->objWhere == null){
			exit;
		}
		/****** caso exista a chamada do metodo where() continua ********/
		$values_update = $this->objSet->getValue();
		/******** monta o where *///////////
		$value_where = $this->objWhere->getValue();
		
		$bindValueWhere = ":where". $value_where["column"];
		$where_operacao .= $value_where["column"];
		$where_operacao .= $this->objWhere->getOperador();
		$where_operacao .= $bindValueWhere;
		$valueBindValueWhere = $value_where["value"];
		
		/******* monta o set *******/
		$arrayBindValuesUpdate;
		foreach($values_update as $key => $value){
			$bindvalue = ":".$value;
			$set .= ",";
			$set .= $key;
			$set .= $this->objSet->getOperador();
			$set .= $bindvalue	;
			$arrayBindValuesUpdate[":".$value] = $value;
		}
		$set = substr($set,1); //remove a primeira virgula 
		$arrayBindValuesUpdate[$bindValueWhere] = $valueBindValueWhere;
		
		//formato sql update:
		//update nometabela set coluna1 = valor1 coluna2 = valor2 where coluna = valor
		$sql = "update $tabela set $set where $where_operacao";

		$pdo = self::pdo();
		$query = $pdo->prepare($sql);
		
		$query->execute($arrayBindValuesUpdate);

	}
	
}