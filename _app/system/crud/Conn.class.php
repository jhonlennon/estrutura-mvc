<?php

    abstract class Conn {

	private static $TablesInfo = array();

	/** @var PDO */
	private static $Connect = null;
	private static $Config;

	public static function setConfig(array $Config = null) {
	    self::$Config = $Config;
	}

	/**
	 * Iniciar uma conexão com o banco de dados e retorna um objeto PDO.
	 * @return PDO
	 */
	private static function Conectar() {
	    try {
		if (self::$Connect == null) {
		    if (!self::$Config) {
			self::$Config = get_config('db' . (IS_LOCAL ? '-local' : null));
		    }
		    $dsn = 'mysql:host=' . self::$Config['host'] . ';dbname=' . self::$Config['database'] . ';charset=utf8';
		    $options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'];
		    self::$Connect = new PDO($dsn, self::$Config['user'], self::$Config['password'], $options);
		    self::$Connect->exec('SET NAMES utf8');
		    self::$Connect->exec('SET CHARACTER SET utf8');
		    self::$Connect->exec('SET time_zone = \'' . date_default_timezone_get() . '\'');
		}
	    } catch (PDOException $e) {
		throw new Exception('Não foi possível conectar a base de dados.');
	    } catch (Exception $e) {
		throw new Exception('Não foi possível conectar a base de dados.');
	    }

	    self::$Connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	    return self::$Connect;
	}

	public static function getDataBaseName() {
	    return self::$Config['database'];
	}

	/**
	 * @return array Lista de tabelas no banco de dados
	 */
	public static function getTables() {
	    $sql = self::getConn();
	    $prepare = $sql->prepare("SHOW TABLES FROM `" . self::$Config['database'] . "`");
	    $prepare->setFetchMode(PDO::FETCH_ASSOC);
	    $prepare->execute();
	    $result = $prepare->fetchAll();
	    foreach ($result as $key => $value) {
		$result[$key] = current($value);
	    }
	    return $result;
	}

	/**
	 * Retorna todas as informações de coluna da tabela informada.
	 * @param string $table
	 * @return array
	 */
	public static function getTableInfo($table = null) {
	    if ($table == null) {
		return self::$TablesInfo;
	    }
	    $table = self::getTableName($table);
	    if (!empty(self::$TablesInfo[$table])) {
		return self::$TablesInfo[$table];
	    }
	    $sql = self::getConn();
	    $prepare = $sql->prepare("SHOW COLUMNS FROM `{$table}`");
	    $prepare->setFetchMode(PDO::FETCH_ASSOC);
	    $prepare->execute();
	    return self::$TablesInfo[$table] = $prepare->fetchAll();
	}

	/**
	 * Retorna o valor do auto increment da tabela
	 * @param string $table
	 * @return int
	 */
	public static function getAutoIncrement($table) {
	    $sql = self::getConn();
	    $prepare = $sql->prepare("SHOW TABLE STATUS LIKE :table");
	    $prepare->bindValue(':table', self::getTableName($table, false));
	    $prepare->execute();
	    if ($prepare->rowCount() > 0) {
		$result = $prepare->fetch();
		return (int) $result['Auto_increment'];
	    }
	    return 0;
	}

	/**
	 * Verifica se uma determinada tabela existe na base de dados.
	 * @return boolean 
	 */
	public static function tableExsits($table) {
	    return (boolean) in_array(self::getTableName($table), self::getTables());
	}

	/**
	 * Retorna o nome correto da tabela com o prefixo adicionado
	 * @return string
	 */
	public static function getTableName($table) {
	    $names = explode('.', $table);
	    $table = str_replace('`', '', end($names));
	    if (!empty(self::$Config['prefixo']) and strpos($table, self::$Config['prefixo']) !== 0) {
		$table = self::$Config['prefixo'] . $table;
	    }
	    if (count($names) == 2) {
		$table = "{$table} AS {$names[0]}";
	    }
	    return (string) $table;
	}

	/**
	 * Retorna o tipo de valor do parâmetro
	 * @param mixed $value
	 * @return int
	 */
	public static function getParamType($value) {
	    if (is_int($value)) {
		return PDO::PARAM_INT;
	    } else if (is_bool($value)) {
		return PDO::PARAM_BOOL;
	    } else if (is_null($value)) {
		return PDO::PARAM_NULL;
	    } else {
		return PDO::PARAM_STR;
	    }
	}

	/**
	 * Retorna um objeto PDO Singleton Pattern. 
	 * @return PDO
	 */
	public static function getConn() {
	    return self::Conectar();
	}

	/**
	 * 
	 * @param string $Table
	 * @param array $values
	 * @return array
	 */
	public static function format_values($Table, array $values = null) {

	    if (is_null($values)) {
		return $values;
	    } else {
		foreach ($values as $key => $value) {
		    if (is_null($value)) {
			$values[$key] = '';
		    }
		}
	    }


	    $infoTable = self::getTableInfo($Table);

	    $result = [];

	    foreach ($infoTable as $info) {

		$field = $info['Field'];

		# Verificando campo
		if (isset($values[$field])) {

		    # Valor passado
		    $value = $values[$field];

		    # Tipo de dado
		    $type = strtolower(current(explode('(', $info['Type'])));

		    # Formatando tipos de valores
		    if (trim($value) !== '') {
			switch ($type) {
			    case 'date':
				$value = Date::data($value);
				break;
			    case 'tinyint':
			    case 'smallint':
			    case 'mediumint':
			    case 'bigint':
			    case 'int':
				$value = Number::int($value);
				break;
			    case 'decimal':
			    case 'float':
			    case 'double':
			    case 'real':
				$value = Number::float($value);
				break;
			    case 'datetime':
			    case 'timestamp':
				$value = Date::timestamp($value);
				break;
			    case 'time':
				$value = Date::time($value);
				break;
			}
		    }
		    # Campo vazio
		    else {
			if ($info['Null'] == 'NO') {
			    $value = '';
			} else if ($info['Null'] == 'YES') {
			    $value = null;
			}
		    }

		    # Setando valor na array
		    $result[$field] = $value;
		}
	    }

	    return $result;
	}

    }
    