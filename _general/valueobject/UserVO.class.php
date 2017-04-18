<?php

    class UserVO extends ValueObject {

	private $Nome;
	private $Email;
	private $Telefone;
	private $Celular;
	private $Login;
	private $Senha;
	private $Type;
	private $TypeTitle;
	private $Permissoes;
	private $PositionUpdate;
	private $Latitude;
	private $Longitude;
	private $Status = 1;

	function getNome() {
	    return (string) $this->Nome;
	}

	function getEmail() {
	    return VALIDAR::email($this->Email) ? strtolower((string) $this->Email) : null;
	}

	function getTelefone() {
	    return Mask::telefone($this->Telefone);
	}

	function getCelular() {
	    return Mask::telefone($this->Celular);
	}

	function getLogin() {
	    return (string) $this->Login;
	}

	function getSenha($hidden = false) {
	    return $hidden ? null : $this->Senha;
	}

	function getType() {
	    return (int) $this->Type;
	}

	function getTypeTitle() {
	    return $this->TypeTitle;
	}

	function getPermissoes() {
	    return $this->Permissoes;
	}

	function getPermissoesAll($toArray = false) {
	    $permissoes = [];
	    foreach ((new MenuModel)->Lista("
                WHERE a.status != 99 
                AND (
                    a.permissao LIKE CONCAT('%[',:type,']%') OR
                    :paginas LIKE CONCAT('%[',a.id,']%')
                )", [
		'type' => $this->getType(),
		'paginas' => $this->getPermissoes(),
	    ]) as $menu) {
		$permissoes[] = $menu->getId();
	    }
	    return $this->formatValue($permissoes, 'array', $toArray);
	}

	function getPositionUpdate($formatar = false) {
	    return $this->formatValue($this->PositionUpdate, 'timestamp', $formatar);
	}

	function getLatitude() {
	    return (float) $this->Latitude;
	}

	function getLongitude() {
	    return (float) $this->Longitude;
	}

	function getStatus() {
	    return $this->Status;
	}

	function setNome($Nome) {
	    $this->Nome = $Nome;
	}

	function setEmail($Email) {
	    $this->Email = $Email;
	}

	function setTelefone($Telefone) {
	    $this->Telefone = $Telefone;
	}

	function setCelular($Celular) {
	    $this->Celular = $Celular;
	}

	function setLogin($Login) {
	    $this->Login = $Login;
	}

	function setSenha($Senha) {
	    $this->Senha = $Senha;
	}

	function setType($Type) {
	    $this->Type = $Type;
	}

	function setTypeTitle($TypeTitle) {
	    $this->TypeTitle = $TypeTitle;
	}

	function setPermissoes($Permissoes) {
	    $this->Permissoes = $Permissoes;
	}

	function setPositionUpdate($PositionUpdate) {
	    $this->PositionUpdate = $PositionUpdate;
	}

	function setLatitude($Latitude) {
	    $this->Latitude = $Latitude;
	}

	function setLongitude($Longitude) {
	    $this->Longitude = $Longitude;
	}

	function setStatus($Status) {
	    $this->Status = $Status;
	}

	function check() {

	    /* @var UsersModel */
	    $model = APP::getInstance('UsersModel');


	    # Tipo de conta
	    if (!$model->getType($this->getType())) {
		throw new Exception('Tipo inválido.');
	    }

	    # E-mail
	    if ($this->Email and ! $this->getEmail()) {
		throw new Exception('E-mail inválido.');
	    } else if ($this->getEmail() and count($model->Lista('WHERE a.email = :email AND a.id != :id LIMIT 1', ['email' => $this->getEmail(), 'id' => $this->getId()]))) {
		throw new Exception('E-mail já está sendo utilizado por outro usuário.');
	    }

	    # Login
	    if (is_string($teste = VALIDAR::username($this->Login))) {
		throw new Exception(strip_tags($teste));
	    } else if ($model->Lista('WHERE a.login = :login AND a.id != :id LIMIT 1', ['login' => $this->getLogin(), 'id' => $this->getId()])) {
		throw new Exception('Esse login já está sendo utilizado.');
	    }

	    # Criptografando a senha
	    if (strlen($this->Senha) != PASSWORD_LENG) {
		if (is_string($teste = VALIDAR::password($this->Senha))) {
		    throw new Exception(strip_tags($teste));
		}
		$this->Senha = password($this->Senha);
	    }

	    return true;
	}

    }
    