<?php

    abstract class Model {

        /** @var array */
        protected $Cache = [];

        /** @var string */
        protected $Query;

        /** @var string Nome da tabela */
        protected $Table;

        /** @var string */
        protected $ValueObject;

        /** @var Read */
        private static $Read;

        /** @var Delete */
        private static $Delete;

        /** @var Update */
        private static $Update;

        /** @var Create */
        private static $Create;

        /**
         * Salva os dados na base de dados
         * @param ValueObject|array $Dados
         */
        public function Save($Dados) {

            # ValueObject
            if (is_object($Dados) and is_a($Dados, 'ValueObject')) {

                # Verificando dados
                try {
                    if (method_exists($Dados, 'check')) {
                        $Dados->check();
                    }
                }
                # Inconsistencia nos dados
                catch (Exception $ex) {
                    throw new Exception($ex->getMessage());
                    return false;
                }

                # Array
                $values = $Dados->toArray();
            }
            # Dados inválido
            else if (!is_array($Dados)) {
                throw new Exception('Os dados precisam ser passado em um Array ou ValueObject.');
                return false;
            } else {
                $values = $Dados;
            }

            # Update
            if (isset($values['id']) and $values['id'] > 0) {
                return self::_Update($this->Table, $values, 'WHERE `id` = :id LIMIT 1', ['id' => (int) $values['id']]);
            }
            # Insert
            else {

                $values['id'] = null;

                $id = self::_Insert($this->Table, $values);

                if ($id > 0 and is_object($Dados)) {
                    $Dados->setId($id);
                }

                return $id;
            }
        }

        /**
         * Atualiza status para 99: Excluído 
         * @param int|ValueObject $id
         * @throws Exception
         */
        public function Status99($id) {
            if ($v = $this->getByLabel('id', $id)) {
                $this->_Update($this->Table, ['status' => 99], "WHERE a.id = :id LIMIT 1", ['id' => is_array($v) ? $v['id'] : $v->getId()]);
            } else {
                throw new Exception('Registro inválido.');
            }
        }

        /**
         * Excluí registro da tabela
         * @param int|ValueObject $VO_or_ID
         * @return boolean
         */
        public function Excluir($VO_or_ID = null) {
            try {

                $Value = $VO_or_ID;

                # Value Object
                if (is_a($Value, 'ValueObject')) {
                    if ($Value->getTable() != $this->Table) {
                        throw new Exception('Inválido: ' . $Value->getTable() . ' != ' . $this->Table);
                    } else if (!$Value->getId()) {
                        throw new Exception('Registro não se encontra na base de dados.');
                    }
                    $id = $Value->getId();
                }
                # Int ou String
                else if (is_int($Value) or is_string($Value)) {
                    $id = $Value;
                }
                # Tipo inválido
                else {
                    throw new Exception('Precisa ser passado um ValueObject ou um Int para excluir um registro.');
                }

                # Excluíndo
                $this->_Delete($this->Table, 'WHERE `id` = :id LIMIT 1', ['id' => $id]);

                # Excluí todas as imagens da ValueObject
                if (is_object($Value)) {
                    $Value->imgDeleteAll();
                }
            } catch (Exception $ex) {
                throw new Exception($ex->getMessage());
            }
        }

        /**
         * Cria um objeto ValueObject para a Model Atual
         * @return ValueObject|null
         */
        public function newValueObject(array $Values = null) {
            /* @var $vo ValueObject */
            if ($class = $this->ValueObject) {
                $vo = new $class;
                $vo->__setTable($this->getTable());
                $vo->__setModel($this);
                $vo->__setValues($Values);
                return $vo;
            } else {
                throw new Exception('Não foi informado a ValueObject.');
            }
        }

        /**
         * Query
         * @return string
         */
        public function getQuery() {
            $Query = str_replace('#table#', $this->getTable(), !empty($this->Query) ? $this->Query : 'SELECT DISTINCT a.* FROM `#table#` AS a');
            if (!preg_match('/SELECT DISTINCT/', $Query)) {
                return preg_replace('/SELECT /', 'SELECT DISTINCT ', $Query);
            }
            return $Query;
        }

        /**
         * Retorna registro pela label
         * @param type $Label
         * @param type $Value
         * @param boolean $ReturnFirst
         * @return null|array|ValueObject
         */
        public function getByLabel($Label, $Value = null, $ReturnFirst = true) {
            # Verificando o cache
            if (isset($this->Cache[$key = sha1("{$Label}:{$Value}")])) {
                return $ReturnFirst ? $this->Cache[$key] : [$this->Cache[$key]];
            }

            # Buscando
            $result = $this->Lista("WHERE a.{$Label} = :value" . ($ReturnFirst ? ' LIMIT 1' : null), ['value' => $Value]);

            if ($ReturnFirst) {
                return count($result) ? $result[0] : null;
            } else {
                return $result;
            }
        }

        /**
         * Lista 
         * @param string $Termos
         * @param array $Places
         * @param boolean $Count
         * @param boolean $toValueObject Se true retorna os registros dentro da ValueObject
         * @return array|int
         */
        public function Lista($Termos = '', array $Places = null, $Count = false, $toValueObject = true) {
            $Query = "{$this->getQuery()} {$Termos}";
            if (!$Count) {
                $result = $this->_Select($Query, $Places);
                if ($toValueObject and $this->ValueObject or is_string($toValueObject)) {
                    return $this->toValueObject($result, is_string($toValueObject) ? $toValueObject : $this->ValueObject, $this->getTable());
                } else {
                    return $result;
                }
            } else {
                $Query = preg_replace('/SELECT.*?FROM/is', 'SELECT COUNT(*) AS `total` FROM', $Query);
                $result = $this->_Select($Query, $Places);
                return count($result) ? $result[0]['total'] : 0;
            }
        }

        /**
         * 
         * @param string $Termos
         * @param array $Places
         * @param int $CurrentPage
         * @param int $PorPagina
         * @return \Pagination
         */
        public function ListaPagination($Termos = null, array $Places = null, $CurrentPage = 1, $PorPagina = 20) {
            $p = new Pagination;

            $p->_Termos = $Termos;
            $p->_Places = $Places;

            $p->setCount($this->Lista($Termos, $Places, true))
                    ->setPorPagina($PorPagina)
                    ->setCurrentPage($CurrentPage)
                    ->setRegistros($this->Lista("{$Termos} {$p->getLimitOffset()}", $Places, false), false);
            return $p;
        }

        /**
         * Table
         * @return string
         */
        public function getTable() {
            return $this->Table;
        }

        /**
         * PDO::Read
         * @return Read
         */
        public static function pdoRead() {
            return self::$Read ? self::$Read : self::$Read = new Read;
        }

        /**
         * PDO::Delete
         * @return Delete
         */
        public static function pdoDelete() {
            return self::$Delete ? self::$Delete : self::$Delete = new Delete;
        }

        /**
         * PDO::Update
         * @return Update
         */
        public static function pdoUpdate() {
            return self::$Update ? self::$Update : self::$Update = new Update;
        }

        /**
         * PDO::Create
         * @return Create
         */
        public static function pdoCreate() {
            return self::$Create ? self::$Create : self::$Create = new Create;
        }

        /**
         * 
         * @param array $MysqlResult
         * @param string $ValueObjectName
         * @param string $Table
         * @return array|ValueObject
         */
        protected function toValueObject(array $MysqlResult, $ValueObjectName, $Table) {
            /* @var $Vo ValueObject */
            if (empty($MysqlResult)) {
                return $MysqlResult;
            } else if (is_array(current($MysqlResult))) {
                foreach ($MysqlResult as $key => $v) {
                    $Vo = new $ValueObjectName;
                    $Vo->__setTable($Table);
                    $Vo->__setModel($this);
                    $Vo->__setValues($v);
                    $this->Cache[sha1("id:{$Vo->getId()}")] = $Vo;
                    $MysqlResult[$key] = $Vo;
                }
            } else {
                $Vo = new $ValueObjectName;
                $Vo->setTable($Table);
                $Vo->__setValues($MysqlResult);
                return $Vo;
            }
            return $MysqlResult;
        }

        /**
         * Mysql Select
         * @param string $Query
         * @param array $Places
         * @return array
         */
        protected static function _Select($Query, array $Places = null) {
            return self::pdoRead()->FullRead($Query, $Places)->getResult();
        }

        /**
         * Mysql Delete
         * @param string $Tabela
         * @param string $Termos
         * @param array $Places
         * @return boolean
         */
        protected static function _Delete($Tabela, $Termos = null, array $Places = null) {
            return self::pdoDelete()->ExeDelete($Tabela, $Termos, $Places)->getResult();
        }

        /**
         * Mysql Update
         * @param string $Tabela
         * @param array $Dados
         * @param string $Termos
         * @param array $Places
         * @return boolean
         */
        protected static function _Update($Tabela, array $Dados, $Termos = null, array $Places = null) {
            return self::pdoUpdate()->ExeUpdate($Tabela, $Dados, $Termos, $Places)->getResult();
        }

        /**
         * Mysql Insert
         * @param string $Tabela
         * @param array $Dados
         * @return int|false
         */
        protected static function _Insert($Tabela, array $Dados) {
            return self::pdoCreate()->ExeCreate($Tabela, $Dados)->getResult();
        }

    }
    