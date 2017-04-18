<?php

    class ServicoVO extends ValueObject {

	private $Insert;
	private $Update;
	private $Title;
	private $Texto;
	private $Status = 1;

	function getInsert($formatar = false) {
	    return $this->formatValue($this->Insert, 'timestamp', $formatar);
	}

	function getUpdate($formatar = false) {
	    return $this->formatValue($this->Update, 'timestamp', $formatar);
	}

	function getTitle() {
	    return $this->Title;
	}

	function getTexto() {
	    return $this->Texto;
	}

	function getStatus() {
	    return (int) $this->Status;
	}

	function setInsert($Insert) {
	    $this->Insert = $Insert;
	}

	function setUpdate($Update) {
	    $this->Update = $Update;
	}

	function setTitle($Title) {
	    $this->Title = $Title;
	}

	function setTexto($Texto) {
	    $this->Texto = $Texto;
	}

	function setStatus($Status) {
	    $this->Status = $Status;
	}

	function check() {
	    if (!$this->getId()) {
		$this->Insert = $this->Update = __NOW__;
	    } else {
		$this->Update = __NOW__;
	    }
	}

    }
    