<?php

    class APP {

	/** @var array Lista de modulos  */
	private static $Modules;

	/** @var string Modulo atual */
	private static $CurrentModule;

	/** @var array Configurações da Aplicação */
	private static $APP_CONFIG;

	/** @var string Modulo padrão */
	private static $DefaultModule;

	/** @var Model Modulo atual */
	private static $Module;

	/** @var Controller Controller atual */
	private static $Controller;

	/** @var string Ação atual */
	private static $Action;

	/** @var string Parâmetros da URL */
	private static $Parans;

	/** @var array Irá guardar uma instância única dos objetos */
	private static $Instances = [];

	/** @var string EX: http://localhost:8090 */
	private static $BaseURL;

	/** @var array Guarda valores globais */
	private static $Globals = [];

	/**
	 * Retorna as configurações da aplicação
	 * @return array
	 */
	public static function getConfig() {
	    return self::$APP_CONFIG;
	}

	/**
	 * URL de base dos arquivos publicos
	 * @return string
	 */
	public static function getBaseUrl() {
	    if (self::$BaseURL) {
		return self::$BaseURL;
	    } else {
		return self::$BaseURL = preg_replace(['/\/$/', '/\?.*/'], null, str_replace(!empty($_GET['GET_VARS']) ? "/{$_GET['GET_VARS']}" : null, null, "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"));
	    }
	}

	/**
	 * Retorna uma instancia Model
	 * @param string $NameClass
	 * @param string $Path
	 * @return Model
	 */
	public static function getInstanceModel($NameClass, $Path = null) {
	    return APP::getInstance(preg_replace('/Model$/', null, $NameClass) . 'Model', $Path);
	}

	/**
	 * Retorna uma instancia Controller
	 * @param string $NameClass
	 * @param string $Path
	 * @return Controller
	 */
	public static function getInstanceController($NameClass, $Path = null) {
	    return APP::getInstance(strtolower(preg_replace('/Controller$/', null, $NameClass)) . 'Controller', $Path);
	}

	/**
	 * Retorna a instância única do objecto solicitado (Singleton)
	 * @param string $NameClass
	 * @return object
	 */
	public static function getInstance($NameClass, $Path = null, array $Parametros = []) {

	    # Chave da instancia
	    $key = $NameClass;

	    if (isset(self::$Instances[$key])) {
		return self::$Instances[$key];
	    } else {
		# Adicionando Barra ao Final
		if (!preg_match('/[\/\\\]$/', $Path)) {
		    $Path .= '/';
		}
		# Criando
		if (__autoload("{$Path}{$NameClass}")) {
		    return APP::$Instances[$key] = (new ReflectionClass($NameClass))->newInstanceArgs($Parametros);
		}
		# Não existe
		else {
		    throw new Exception("A class `{$NameClass}` não existe.");
		}
	    }
	    return null;
	}

	/**
	 * Retorna os Modulos
	 * @param string $Modulo
	 * @param boolean $Path
	 * @return boolean
	 */
	static function getModules($Modulo = null, $Path = false) {
	    if ($Modulo === null) {
		return self::$Modules;
	    } else {
		if (isset(self::$Modules[$Modulo])) {
		    if ($Path) {
			$Modulo = self::$Modules[$Modulo];
			if (is_array($Modulo)) {
			    return $Modulo['path'];
			} else {
			    return $Modulo;
			}
		    } else {
			$Modulo;
		    }
		} else {
		    return null;
		}
	    }
	}

	/**
	 * Retorna as configurações do Módulo
	 * @param string $Module Se não for informado retorna do modulo atual
	 * @return mixed
	 */
	static function getModuleConfig($Module = null) {
	    if ($Module === null) {
		$Module = self::getCurrentModule();
	    }
	    return self::getModules()[self::getCurrentModule()];
	}

	/**
	 * Retorn o Módulo atual
	 * @return string
	 */
	static function getCurrentModule($Path = false) {
	    return !$Path ? self::$CurrentModule : self::getModules(self::$CurrentModule, $Path);
	}

	/**
	 * Retorna o nome ou a pasta do modulo
	 * @param boolean $Path
	 * @return string
	 */
	static function getDefaultModule($Path = false) {
	    return !$Path ? self::$DefaultModule : self::getModules(self::$DefaultModule, $Path);
	}

	/**
	 * Retorna a pasta da aplicação
	 * @return string
	 */
	static function getPathApplication() {
	    return self::$PathApplication;
	}

	/**
	 * Retorna o Modulo em execução
	 * @return Module
	 */
	static function getModule() {
	    return self::$Module;
	}

	/**
	 * Retorna a controller atual
	 * @return Controller
	 */
	static function getController() {
	    return self::$Controller;
	}

	/**
	 * 
	 * @param string $Controller
	 * @return Controller
	 */
	static function setController($Controller) {
	    self::$Controller = APP::getInstanceController($Controller);
	    return self::$Controller;
	}

	/**
	 * 
	 * @return type
	 */
	static function getControllerName() {
	    return preg_replace('/^(.*(\\|\/))?(.*)Controller$/', '$3', get_class(self::$Controller));
	}

	/**
	 * Retorna os parâmetros da URL
	 * @return array
	 */
	static function getParans() {
	    return self::$Parans;
	}

	/**
	 * Retorna a Action em execução
	 * @return string
	 */
	static function getAction() {
	    return self::$Action;
	}

	/**
	 * Inicia a aplicação
	 * @param array $APPConfig
	 * @param string $PublicPath
	 */
	static function Initialize(array $APPConfig, $PublicPath = 'public_html') {

	    APP::setGlobal('public_path', $PublicPath);
	    $Action = $Controller = null;
	    self::$APP_CONFIG = $APPConfig;
	    self::$Modules = isset(self::$APP_CONFIG['modules']) ? self::$APP_CONFIG['modules'] : self::$APP_CONFIG;
	    self::$DefaultModule = key(self::$Modules);

	    # URL Variables
	    $values = !empty($_GET['GET_VARS']) ? explode('/', strtolower($_GET['GET_VARS'])) : [self::$DefaultModule, 'index', 'index'];

	    # Modulo
	    if (isset(self::$Modules[$values[0]])) {
		self::$CurrentModule = $values[0];
		unset($values[0]);
		$values = array_values($values);
	    } else {
		self::$CurrentModule = self::$DefaultModule;
	    }


	    # Controller
	    if (empty($values[0]) or controller_exists(strtolower($values[0])) === false) {
		$Controller = 'index';
	    } else {
		$Controller = $values[0];
		unset($values[0]);
		$values = array_values($values);
	    }

	    # Action
	    if (empty($values[0]) and controller_action_exists($Controller, 'index')) {
		$Action = 'indexAction';
	    } else if (empty($values[0]) or ! ($Action = controller_action_exists($Controller, $values[0]))) {
		self::$Parans['action'] = strtolower(@$values[0]);
		$Action = 'notFoundPage';
	    } else {
		unset($values[0]);
		$values = array_values($values);
	    }

	    # Parâmetros númericos
	    self::$Parans = $values;

	    # Parâmetros nomeados
	    if (count(self::$Parans) > 1) {
		for ($i = 0; $i < count($values); $i+=2) {
		    if (isset($values[$i + 1])) {
			self::$Parans[$values[$i]] = $values[$i + 1];
		    }
		}
	    }

	    # Modulo
	    if (!isset(self::$Parans['module'])) {
		self::$Parans['module'] = APP::getCurrentModule();
	    }

	    # Controller
	    if (!isset(self::$Parans['controller'])) {
		self::$Parans['controller'] = APP::getControllerName();
	    }

	    # Action
	    if (!isset(self::$Parans['action'])) {
		self::$Parans['action'] = $Action;
	    }

	    # Valores GET
	    if (preg_match('/\?/', inputServer('REQUEST_URI'))) {
		parse_str(preg_replace('/.*\?/', NULL, inputServer('REQUEST_URI')), $getValues);
		foreach ($getValues as $key => $value) {
		    $_GET[$key] = $value;
		    if (!isset(self::$Parans[$key])) {
			self::$Parans[$key] = $value;
		    }
		}
	    }

	    # Action
	    self::$Action = $Action;

	    # Cria instancia do modulo
	    if (file_exists($ModuleClass = ABSPATH . '/' . self::getCurrentModule(true) . '/Module.class.php')) {
		file_include($ModuleClass);
		self::$Module = new Module;
	    }

	    # Executando a action
	    self::setController($Controller);
	    self::getController()->$Action();
	}

	/**
	 * Set um valor global
	 * @param string|int $key
	 * @param mixed $value
	 * @return mixed Retorna o valor informado
	 */
	public static function setGlobal($key, $value) {
	    self::$Globals[$key] = $value;
	    return $value;
	}

	/**
	 * Retorna um valor global
	 * @param string|int $key
	 * @return mixed
	 */
	public static function getGlobal($key = null) {
	    return $key !== null ? (isset(self::$Globals[$key]) ? self::$Globals[$key] : null) : self::$Globals;
	}

	/**
	 * 
	 * @param string $Path Completa o direitorio a partir de public
	 * @return string
	 */
	public static function getPublicPath($Path = null) {
	    return APP::getGlobal('public_path') . ($Path ? "/{$Path}" : null);
	}

    }
    