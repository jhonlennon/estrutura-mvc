<?php
    
    class Create extends Conn {

	/** @var string */
	private $Tabela;

	/** @var array */
	private $Dados;

	/** 	@var int */
	private $Result;

	/** @var PDOStatement */
	private $Create;

	/** @var PDO */
	private $Conn;

	/**
	 * <b>ExeCreate:</b> Executa um cadastro simplificado no banco de dados utilizando prepared statements.
	 * Basta informar o nome da tabela e um array atribuitivo com nome da coluna e valor!
	 * 
	 * @param STRING $Tabela = Informe o nome da tabela no banco!
	 * @param ARRAY $Dados = Informe um array atribuitivo. ( Nome Da Coluna => Valor ).
         * @return \Create
	 */
	public function ExeCreate($Tabela, array $Dados) {
	    $this->Tabela = (string) Conn::getTableName($Tabela);
	    $this->Dados = self::format_values($Tabela, $Dados);
	    if(empty($this->Dados)){
		throw new Exception("A array de dados está vazia. Tentativa de salvar dados na tabela `{$Tabela}` não foi concluída.");
	    }
	    $this->getSyntax();
	    $this->Execute();
            return $this;
	}

	/**
	 * <b>Obter resultado:</b> Retorna o ID do registro inserido ou FALSE caso nem um registro seja inserido! 
	 * @return INT $Variavel = lastInsertId OR FALSE
	 */
	public function getResult() {
	    return $this->Result;
	}

	/**
	 * ****************************************
	 * *********** PRIVATE METHODS ************
	 * ****************************************
	 */
	//Obtém o PDO e Prepara a query
	private function Connect() {
	    $this->Conn = parent::getConn();
	    $this->Create = $this->Conn->prepare($this->Create);
	}

	//Cria a sintaxe da query para Prepared Statements
	private function getSyntax() {
	    $Fileds = '`' . implode('`, `', array_keys($this->Dados)) . '`';
	    $Places = ':' . implode(', :', array_keys($this->Dados));
	    $this->Create = "INSERT INTO {$this->Tabela} ({$Fileds}) VALUES ({$Places})";
	}

	//Obtém a Conexão e a Syntax, executa a query!
	private function Execute() {
	    $this->Connect();
	    try {
		$this->Create->execute($this->Dados);
		$this->Result = $this->Conn->lastInsertId();
	    } catch (PDOException $e) {
		$this->Result = null;
		trigger_error("<b>Erro ao cadastrar:</b> {$e->getMessage()}<br>{$this->Create->queryString}", $e->getCode());
	    }
	}

    }
    