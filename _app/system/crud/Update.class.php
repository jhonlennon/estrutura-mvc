<?php

    class Update extends Conn {

        private $Tabela;
        private $Dados;
        private $Termos;
        private $Places;
        private $Result;
        private $Syntax;

        /** @var PDOStatement */
        private $Update;

        /** @var PDO */
        private $Conn;

        /**
         * <b>Exe Update:</b> Executa uma atualização simplificada com Prepared Statments. Basta informar o 
         * nome da tabela, os dados a serem atualizados em um Attay Atribuitivo, as condições e uma 
         * analize em cadeia (ParseString) para executar.
         * @param STRING $Tabela = Nome da tabela
         * @param ARRAY $Dados = [ NomeDaColuna ] => Valor ( Atribuição )
         * @param STRING $Termos = WHERE coluna = :link AND.. OR..
         * @param STRING $ParseString = link={$link}&link2={$link2}
         * @return \Update
         */
        public function ExeUpdate($Tabela, array $Dados, $Termos = null, array $Places = null) {
            $this->Tabela = (string) $Tabela;
            $this->Dados = (array) self::format_values($Tabela, $Dados);
            $this->Termos = $Termos;
            $this->Places = $Places;
            $this->getSyntax();
            $this->Execute();
            return $this;
        }

        /**
         * <b>Obter resultado:</b> Retorna TRUE se não ocorrer erros, ou FALSE. Mesmo não alterando os dados se uma query
         * for executada com sucesso o retorno será TRUE. Para verificar alterações execute o getRowCount();
         * @return BOOL $Var = True ou False
         */
        public function getResult() {
            return $this->Result;
        }

        /**
         * <b>Contar Registros: </b> Retorna o número de linhas alteradas no banco!
         * @return INT $Var = Quantidade de linhas alteradas
         */
        public function getRowCount() {
            return $this->Update->rowCount();
        }

        /**
         * <b>Modificar Links:</b> Método pode ser usado para atualizar com Stored Procedures. Modificando apenas os valores
         * da condição. Use este método para editar múltiplas linhas!
         * @param STRING $ParseString = id={$id}&..
         * @return \Update
         */
        public function setPlaces($Places) {
            $this->Places = self::format_values($Places);
            $this->getSyntax();
            $this->Execute();
            return $this;
        }

        /**
         * ****************************************
         * *********** PRIVATE METHODS ************
         * ****************************************
         */

        /**
         * Obtém o PDO e Prepara a query
         */
        private function Connect() {
            $this->Conn = parent::getConn();
            $this->Update = $this->Conn->prepare($this->Syntax);
        }

        /**
         * Cria a sintaxe da query para Prepared Statements
         */
        private function getSyntax() {
            foreach ($this->Dados as $Key => $Value) {
                $Places[] = "`{$Key}` = :{$Key}";
            }
            $Places = implode(', ', $Places);
            $this->Syntax = "UPDATE `{$this->Tabela}` AS a SET {$Places} {$this->Termos}";
        }

        /**
         * Obtém a Conexão e a Syntax, executa a query!
         */
        private function Execute() {
            $this->Connect();
            try {
                $this->Update->execute(array_merge((array) $this->Dados, (array) $this->Places));
                $this->Result = true;
            } catch (PDOException $e) {
                $this->Result = null;
                var_dump($this->Syntax);
                trigger_error($e->getMessage(), $e->getCode());
            }
        }

    }
    