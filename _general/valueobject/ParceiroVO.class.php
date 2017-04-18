<?php

    class ParceiroVO extends ValueObject {

	private $Insert;
	private $Update;
	private $Nome;
	private $Link;
	private $Status = 1;

	function getInsert($formatar = false) {
	    return $this->formatValue($this->Insert, 'timestamp', $formatar);
	}

	function getUpdate($formatar = false) {
	    return $this->formatValue($this->Update, 'timestamp', $formatar);
	}

	function getNome() {
	    return $this->Nome;
	}

	function getLink() {
	    return $this->Link;
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

	function setNome($Nome) {
	    $this->Nome = $Nome;
	}

	function setLink($Link) {
	    $this->Link = $Link;
	}

	function setStatus($Status) {
	    $this->Status = $Status;
	}

	function check() {
	    if (!$this->getId()) {
		$this->Update = $this->Insert = __NOW__;
	    } else {
		$this->Update = __NOW__;
	    }
	}

    }
    