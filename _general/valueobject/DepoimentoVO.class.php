<?php

    class DepoimentoVO extends ValueObject {

	private $Insert;
	private $Nome;
	private $Depoimento;
	private $Status = 1;

	function getInsert($format = false) {
	    return $format ? Date::formatDataTime($this->Insert) : Date::timestamp($this->Insert);
	}

	function getNome() {
	    return $this->Nome;
	}

	function getDepoimento() {
	    return $this->Depoimento;
	}

	function getStatus() {
	    return $this->Status;
	}

	function setInsert($Insert) {
	    $this->Insert = $Insert;
	}

	function setNome($Nome) {
	    $this->Nome = $Nome;
	}

	function setDepoimento($Depoimento) {
	    $this->Depoimento = $Depoimento;
	}

	function setStatus($Status) {
	    $this->Status = $Status;
	}

	public function __check() {
	    if (!$this->getId()) {
		$this->setInsert(date('Y-m-d H:i:s'));
	    }
	}

    }
    