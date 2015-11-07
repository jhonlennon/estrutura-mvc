<?php

    class Read extends Conn {

	private $Select;
	private $Places;
	private $Result;

	/** @var PDOStatement */
	private $Read;

	/** @var PDO */
	private $Conn;

	/**
	 * <b>Exe Read:</b> Executa uma leitura simplificada com Prepared Statments. Basta informar o nome da tabela,
	 * os termos da seleção e uma analize em cadeia (ParseString) para executar.
	 * @param STRING $Tabela = Nome da tabela
	 * @param STRING $Termos = WHERE | ORDER | LIMIT :limit | OFFSET :offset
	 * @param STRING $ParseString = link={$link}&link2={$link2}
	 * @return Conn
	 */
	public function ExeRead($Tabela, $Termos = null, $Places = null) {
	    $this->Places = (array) $Places;
	    $this->Select = "SELECT * FROM " . parent::getTableName($Tabela) . " {$Termos}";
	    $this->Execute();
	    return $this;
	}

	/**
	 * <b>Obter resultado:</b> Retorna um array com todos os resultados obtidos. Envelope primário númérico. Para obter
	 * um resultado chame o índice getResult()[0]!
	 * @return ARRAY $this = Array ResultSet
	 */
	public function getResult() {
	    return $this->Result;
	}

	/**
	 * <b>Contar Registros: </b> Retorna o número de registros encontrados pelo select!
	 * @return INT $Var = Quantidade de registros encontrados
	 */
	public function getRowCount() {
	    return $this->Read->rowCount();
	}

	/**
	 * 
	 * @param type $Query
	 * @param type $Places
	 * @return \Read
	 */
	public function FullRead($Query, $Places = null) {
	    $this->Select = (string) $Query;
	    $this->Places = (array) $Places;
	    $this->Execute();
	    return $this;
	}

	/**
	 * <b>Full Read:</b> Executa leitura de dados via query que deve ser montada manualmente para possibilitar
	 * seleção de multiplas tabelas em uma única query!
	 * @param STRING $Query = Query Select Syntax
	 * @param STRING $ParseString = link={$link}&link2={$link2}
	 * @return \Read
	 */
	public function setPlaces($Places) {
	    $this->Places = (array) $Places;
	    $this->Execute();
	    return $this;
	}

	/**
	 * ****************************************
	 * *********** PRIVATE METHODS ************
	 * ****************************************
	 */
	//Obtém o PDO e Prepara a query
	private function Connect() {
	    $this->Conn = parent::getConn();
	    $this->Read = $this->Conn->prepare($this->Select);
	    $this->Read->setFetchMode(PDO::FETCH_ASSOC);
	}

	//Cria a sintaxe da query para Prepared Statements
	private function getSyntax() {
	    if ($this->Places) {
		foreach ($this->Places as $Vinculo => $Valor) {
		    if ($Vinculo == 'limit' || $Vinculo == 'offset') {
			$Valor = (int) $Valor;
		    }
		    $this->Read->bindValue(":{$Vinculo}", $Valor, Conn::getParamType($Valor));
		}
	    }
	}

	//Obtém a Conexão e a Syntax, executa a query!
	private function Execute() {
	    $this->Connect();
	    try {
		$this->getSyntax();
		$this->Read->execute();
		$this->Result = $this->Read->fetchAll();
	    } catch (PDOException $e) {
		$this->Result = null;
		print nl2br($this->Select);
		trigger_error($e->getMessage());
	    }
	}

    }
    