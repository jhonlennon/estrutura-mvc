<?php

    class SiteConfigVO extends ValueObject {

	private $Title;
	private $Telefone;
	private $Celular;
	private $Email;
	private $Descricao;
	private $Keywords;
	private $Facebook;
	private $FacebookLike;
	private $Cep;
	private $Logradouro;
	private $Numero;
	private $Bairro;
	private $Cidade;
	private $Latitude;
	private $Longitude;
	private $Zoom;

	function getTitle() {
	    return $this->Title;
	}

	function getTelefone() {
	    return Mask::telefone($this->Telefone);
	}

	function getCelular() {
	    return Mask::telefone($this->Celular);
	}

	function getEmail() {
	    return VALIDAR::email($this->Email) ? strtolower($this->Email) : null;
	}

	function getDescricao() {
	    return $this->Descricao;
	}

	function getKeywords() {
	    return $this->Keywords;
	}

	function getFacebook() {
	    return $this->Facebook;
	}

	function getFacebookLike() {
	    return $this->FacebookLike;
	}

	function getCep() {
	    return Mask::cep($this->Cep);
	}

	function getLogradouro() {
	    return $this->Logradouro;
	}

	function getNumero() {
	    return $this->Numero;
	}

	function getBairro() {
	    return $this->Bairro;
	}

	function getCidade() {
	    return (int) $this->Cidade;
	}

	/** @return CidadeVO */
	function voCidade() {
	    return newModel('Cidades')->getByLabel('id', $this->Cidade);
	}

	function getUf() {
	    return $this->voCidade()->getUf();
	}

	function getCidadeTitle() {
	    return $this->voCidade()->getTitle();
	}

	function getEstado() {
	    return (int) $this->voCidade()->getEstado();
	}

	function getEstadoTitle() {
	    return $this->voCidade()->getEstadoTitle();
	}

	function getLatitude() {
	    return (float) $this->Latitude;
	}

	function getLongitude() {
	    return (float) $this->Longitude;
	}

	function getZoom() {
	    return (int) $this->Zoom;
	}

	function setTitle($Title) {
	    $this->Title = $Title;
	}

	function setTelefone($Telefone) {
	    $this->Telefone = $Telefone;
	}

	function setCelular($Celular) {
	    $this->Celular = $Celular;
	}

	function setEmail($Email) {
	    $this->Email = $Email;
	}

	function setDescricao($Descricao) {
	    $this->Descricao = $Descricao;
	}

	function setKeywords($Keywords) {
	    $this->Keywords = $Keywords;
	}

	function setFacebook($Facebook) {
	    $this->Facebook = $Facebook;
	}

	function setFacebookLike($FacebookLike) {
	    $this->FacebookLike = $FacebookLike;
	}

	function setCep($Cep) {
	    $this->Cep = $Cep;
	}

	function setLogradouro($Logradouro) {
	    $this->Logradouro = $Logradouro;
	}

	function setNumero($Numero) {
	    $this->Numero = $Numero;
	}

	function setBairro($Bairro) {
	    $this->Bairro = $Bairro;
	}

	function setCidade($Cidade) {
	    $this->Cidade = $Cidade;
	}

	function setLatitude($Latitude) {
	    $this->Latitude = $Latitude;
	}

	function setLongitude($Longitude) {
	    $this->Longitude = $Longitude;
	}

	function setZoom($Zoom) {
	    $this->Zoom = $Zoom;
	}

	function check() {
	    if (!$this->voCidade()) {
		throw new Exception('Cidade inválida.');
	    }
	}

    }
    