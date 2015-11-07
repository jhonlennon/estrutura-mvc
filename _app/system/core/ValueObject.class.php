<?php

    abstract class ValueObject {

	protected $Id = 0;
	protected $_Values = [];
	protected $Table = null;

	/** @var Model */
	protected $Model;

	/**
	 * Formata o valor
	 * @param mixed $value
	 * @param string $type
	 * @param boolean $formatar
	 * @return mixed
	 */
	function formatValue($value, $type, $formatar) {
	    switch (strtolower($type)) {
		case 'data':
		case 'dt':
		case 'date':
		    $value = Date::data((string) $value);
		    return $formatar ? Date::formatData($value) : $value;
		    break;
		case 'timestamp':
		case 'datatime':
		    $value = Date::timestamp((string) $value);
		    return $formatar ? Date::formatDataTime($value) : $value;
		    break;
		case 'real':
		    return $formatar ? Number::real($value) : Number::float($value, 2);
		case 'hide':
		    return $formatar ? null : $value;
		    break;
		case 'array':
		    return $formatar ? stringToArray($value) : arrayToString($value);
		    break;
		default:
		    return $value;
	    }
	}

	public function __setModel(Model $Model) {
	    $this->Model = $Model;
	}

	function getId() {
	    return $this->Id;
	}

	function getTable() {
	    return $this->Table;
	}

	/**
	 * 
	 * @param int $Id
	 * @return Object
	 */
	function setId($Id) {
	    if (!$this->getId()) {
		$this->Id = (int) $Id;
	    }
	    return $this;
	}

	/**
	 * 
	 * @param string $Table
	 * @return Object
	 */
	function __setTable($Table) {
	    if (!$this->getTable()) {
		$this->Table = $Table;
	    }
	    return $this;
	}

	/**
	 * Retorna a imagem de capa
	 * @param boolean $Default Retorna um default caso ainda não tenha imagem
	 * @param string $ADDReference
	 * @return null|\ImageVO
	 */
	function imgCapa($Default = true, $ADDReference = null) {
	    return APP::getInstanceModel('Imagens')->capa($this->getTable() . (string) $ADDReference, $this->getId(), $Default);
	}

	/**
	 * Adiciona imagem
	 * @param type $FileInputKey
	 * @param type $Last
	 * @param type $ADDReference
	 * @return Object
	 */
	function imgAdd($FileInputKey = null, $Last = true, $ADDReference = null) {
	    /* @var $model ImagensModel */
	    $model = APP::getInstanceModel('ImagensModel');
	    $img = $model->newValueObject([
		'ref' => $this->getTable() . (string) $ADDReference,
		'refid' => $this->getId(),
	    ]);
	    $model->salvaImage($img, $FileInputKey, $Last);
	    return $this;
	}

	/**
	 * Adiciona imagem de capa
	 * @param string $FileInputKey
	 * @param string $ADDReference
	 * @return Object
	 */
	function imgAddCapa($FileInputKey = null, $ADDReference = null) {
	    $this->imgAdd($FileInputKey, false, $ADDReference);
	    return $this;
	}

	/**
	 * Excluí todas as imagens da VO
	 * @return Object
	 */
	function imgDeleteAll() {
	    APP::getInstanceModel('ImagensModel')->excluirTodasImagens($this->getTable(), $this->getId());
	    return $this;
	}

	/**
	 * 
	 * @param boolean $Default
	 * @param string $ADDReference
	 * @return array|ImageVO
	 */
	function imgList($Default = false, $ADDReference = null) {
	    return APP::getInstanceModel('ImagensModel')->getByRef($this->getTable() . (string) $ADDReference, $this->getId(), $Default);
	}

	/**
	 * 
	 * @param type $Name
	 * @param string $MethodKey null | nome metodo da ValueObject
	 * @param string $MethodInput POST | GET
	 * @return Object
	 */
	public function input($Name, $MethodKey = null, $MethodInput = 'POST') {
	    $function = strtoupper($MethodInput) == 'POST' ? 'inputPost' : 'inputGet';
	    $value = call_user_func($function, $Name);
	    $Method = 'set' . preg_replace('/^set/', null, ucfirst(strtolower($MethodKey == null ? $Name : $MethodKey)));
	    if (method_exists($this, $Method)) {
		$this->$Method($value);
	    } else {
		throw new Exception('O método `' . $Method . '` não existe em `' . get_class($this) . '`.');
	    }
	    return $this;
	}

	/**
	 * Retorna um valor get
	 * @param string $Method
	 * @param type $Format
	 * @return type
	 */
	public function get($Method, $Format = false) {
	    # Method get
	    if (method_exists($this, $MethodName = 'get' . $Method)) {
		return $this->$MethodName($Format);
	    }
	    # Valor
	    $key = strtolower($Method);
	    if (isset($this->_Values[$key])) {
		return $this->_Values[$key];
	    }
	    # Valor não encontrado
	    return null;
	}

	/**
	 * Seta um valor
	 * @param string $MethodSetName
	 * @param mixed $Value
	 * @return Object
	 */
	public function set($MethodSetName, $Value) {
	    $Method = strtolower(preg_replace('/^set([A-Z])/', '$1', $MethodSetName));
	    if (method_exists($this, "set{$Method}")) {
		$Method = 'set' . $Method;
		$this->$Method($Value);
	    } else {
		$this->_Values[$Method] = $Value;
	    }
	    return $this;
	}

	/**
	 * Seta todos os valores
	 * @param array $Values
	 * @return Object
	 */
	public function __setValues(array $Values = null) {
	    if ($Values) {
		foreach ($Values as $Method => $Value) {
		    $this->set($Method, $Value);
		}
	    }
	    return $this;
	}

	/**
	 * Retorna os valores do objeto em um array
	 * @param type $format
	 * @return type
	 */
	public function toArray($format = false) {
	    $values = [];

	    # Pegandos valores 
	    foreach (get_class_methods($this) as $method) {
		if (preg_match('/^get[A-Z]/', $method)) {
		    $values[strtolower(preg_replace('/^get/', null, $method))] = $this->$method($format);
		}
	    }

	    # Removendo valores indesejados
	    foreach ($values as $key => $value) {
		if (in_array($key, ['table', 'voreference']) or is_object($value)) {
		    unset($values[$key]);
		}
	    }
	    return $values;
	}

	/**
	 * Retorna os valores do objeto em uma string JSON
	 * @param boolean $format
	 * @return string
	 */
	public function toJason($format = false) {
	    return json_encode($this->toArray($format));
	}

	/**
	 * Retorna os valores do objecto em uma string JSON convertida para HTML
	 * @param boolean $format
	 * @return string
	 */
	public function toHtml($format = true) {
	    return htmlspecialchars($this->toJason($format));
	}

	/**
	 * Retorna o objecto em uma string
	 * @return string
	 */
	public function __toString() {
	    return $this->toHtml(true);
	}

	/**
	 * Retorna o clone do objeto atual
	 * @return Object
	 */
	public function cloneVo() {
	    $clone = clone $this;
	    $clone->Id = 0;
	    return $clone;
	}

	/**
	 * Retorna a Model do Objeto
	 * @return Model
	 * @throws Exception
	 */
	public function _getModel() {
	    if (!$this->Model or ! is_a($this->Model, 'Model')) {
		throw new Exception(get_class($this) . ': Não possuí uma Model.');
	    } else if (!$this->Table) {
		throw new Exception(get_class($this) . ': Não foi informada a tabela do objecto.');
	    } else if ($this->Table != $this->Model->getTable()) {
		throw new Exception(get_class($this) . ": ValueObject.table.{$this->Table} != Model.table.{$this->Model->getTable()}");
	    }
	    return $this->Model;
	}

	/**
	 * Salva os dados do objeto no banco de dados
	 * @param array $setValues
	 * @return Object
	 */
	public function Save(array $setValues = null) {
	    $this->__setValues($setValues)->_getModel()->Save($this);
	    return $this;
	}

	/**
	 * Excluí o Objeto da base de daods.
	 * @return Object
	 */
	public function Excluir() {
	    $this->_getModel()->Excluir($this);
	    $this->Id = 0;
	    return $this;
	}

	function getVoReference() {
	    return strtolower('table-' . $this->getTable() . '-id-' . $this->getId());
	}

    }
    