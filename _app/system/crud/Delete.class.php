<?php

    class Delete extends Conn {

        private $Tabela;
        private $Termos;
        private $Places;
        private $Result;

        /** @var PDOStatement */
        private $Delete;

        /** @var PDO */
        private $Conn;

        /**
         * 
         * @param type $Tabela
         * @param type $Termos
         * @param type $Places
         * @return \Delete
         */
        public function ExeDelete($Tabela, $Termos = null, array $Places = null) {
            $this->Tabela = (string) $Tabela;
            $this->Termos = (string) $Termos;
            $this->Places = $Places;
            $this->getSyntax();
            $this->Execute();
            return $this;
        }

        public function getResult() {
            return $this->Result;
        }

        public function getRowCount() {
            return $this->Delete->rowCount();
        }

        /**
         * 
         * @param type $Places
         * @return \Delete
         */
        public function setPlaces($Places) {
            $this->Places = (array) $Places;
            $this->getSyntax();
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
            $this->Delete = $this->Conn->prepare($this->Delete);
        }

        //Cria a sintaxe da query para Prepared Statements
        private function getSyntax() {
            $this->Delete = "DELETE FROM {$this->Tabela} {$this->Termos}";
        }

        //Obtém a Conexão e a Syntax, executa a query!
        private function Execute() {
            $this->Connect();
            try {
                $this->Delete->execute($this->Places);
                $this->Result = true;
            } catch (PDOException $e) {
                $this->Result = null;
                trigger_error("<b>Erro ao Deletar:</b> {$e->getMessage()}", $e->getCode());
            }
        }

    }
    