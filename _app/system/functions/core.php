<?php

    /**
     * Retornar as configurações
     * @param mixed $key
     * @return mixed
     */
    function get_config($key = null) {
	$globalKey = 'app_config_global';
	$config = APP::getGlobal($globalKey);
	if (!$config) {
	    # config.ini
	    $config = parse_ini_file(ABSPATH . '/config.ini', true);

	    # Config APP
	    if (APP::getCurrentModule()) {

		# Verificando configurações globais
		$appConfig = APP::getConfig();
		if (isset($appConfig['config'])) {
		    $config = extend($config, $appConfig['config']);
		}

		# Verifcando configurações do Módulo
		$appConfig = APP::getModuleConfig();
		if (is_array($appConfig) and isset($appConfig['config'])) {
		    $config = extend($config, $appConfig['config']);
		}

		# Salvando em global
		APP::setGlobal($globalKey, $config);
	    }
	}

	// Buscando chave
	if ($key) {
	    return isset($config[$key]) ? $config[$key] : null;
	}

	return $config;
    }

    /**
     * array_merge inteligente
     * @param array $initial
     * @param array $merge
     * @return array
     */
    function extend(array $initial, array $merge) {
	if ($merge and $initial) {
	    foreach ($merge as $key => $value) {
		if (is_int($key)) {
		    $initial[] = $value;
		} else if (is_array($value) and isset($initial[$key])) {
		    $initial[$key] = extend($initial[$key], $value);
		} else {
		    $initial[$key] = $value;
		}
	    }
	}
	return $initial;
    }

    /**
     * Verifica a existência da controller
     * @param string $Controller
     * @param string $Modulo
     * @return string|false
     */
    function controller_exists($Controller) {
	return _file_exists(preg_replace('/Controller$/', null, $Controller) . 'Controller.class.php', false);
    }

    /**
     * Verifica a existência da action na controller
     * @param string $Controller
     * @param string $Action
     * @return method|boolean
     */
    function controller_action_exists($Controller, $Action) {
	$Action = preg_replace('/Action$/i', null, $Action) . 'Action';
	return method_exists(preg_replace('/Controller$/', null, $Controller) . 'Controller', $Action) ? $Action : null;
    }

    /**
     * 
     * @param type $file
     * @param array $Variables
     * @param boolean $return
     * @param boolean $autoExtractHeaders
     * @return type
     */
    function view_load($file, array $Variables = null, $return = false, $autoExtractHeaders = true) {
	ob_start();

	# Incluindo arquivo
	$fileName = ABSPATH . '/' . APP::getCurrentModule(true) . "/views/{$file}.phtml";
	if (!file_exists($fileName)) {
	    throw new Exception("View `{$file}` não existe.");
	}

	file_include($fileName, $Variables);

	# Retornando HTML
	if ($return) {
	    $Body = ob_get_clean();

	    if ($autoExtractHeaders) {
		# Script
		preg_match_all('/<script.*?<\/script>/is', $Body, $result);
		foreach ($result[0] as $html) {
		    Seo::addJs($html);
		}

		# Style
		preg_match_all('/<style.*?<\/style>/is', $Body, $result);
		foreach ($result[0] as $html) {
		    Seo::addCss($html);
		}

		return preg_replace(['/<style.*?<\/style>/is', '/<script.*?<\/script>/is'], null, $Body);
	    } else {
		return $Body;
	    }
	} else {
	    ob_end_flush();
	}
    }

    /**
     * Inclui arquivo passando variavéis
     * @param string $file
     * @param array $Variables
     */
    function file_include($_include_file, array $_include_vars = null) {
	# Extraindo valores
	if ($_include_vars) {
	    extract($_include_vars);
	    unset($_include_vars);
	}

	# Incluindo arquivo
	if (file_exists($_include_file = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $_include_file))) {
	    include $_include_file;
	}
	# Arquivo não encontrado.
	else {
	    throw new Exception('Arquivo inexistente.');
	}
    }

    /**
     * 
     * @param string $Module
     * @return string
     */
    function get_modules($Module = null) {
	$modules = APP::getModules();
	if ($Module != null) {
	    return isset($modules[$Module]) ? $modules[$Module] : null;
	} else {
	    return $modules;
	}
    }

    /**
     * Verifica se o Modulo atual é o Modulo padrão
     * @param string $Module
     * @return boolean
     */
    function is_default_module($Module = null) {
	return ($Module === null ? get_current_module() : $Module) == get_default_module();
    }

    /**
     * Retorna o modulo atual
     * @return string
     */
    function get_current_module() {
	return APP::getCurrentModule();
    }

    /**
     * Retorna o modulo padrão
     * @return string
     */
    function get_default_module() {
	return APP::getDefaultModule();
    }

    /**
     * Retorna todas as pasta para o include
     * @return array
     */
    function get_all_paths() {
	# Pega o valor já gravado
	if (APP::getGlobal('get_all_paths')) {
	    return APP::getGlobal('get_all_paths');
	}

	# Pastas do sistema
	$Paths = [
	    # Root
	    '',
	    # System
	    'system',
	    'system/core',
	    'system/crud',
	    'system/helpers',
	    'system/helpers/Bootstrap',
	    'system/models',
	    'system/libraries',
	    'system/models',
	    'system/valueobject',
	];

	# Não possuí cache listando pastas
	if (!APP::getCurrentModule()) {
	    return $Paths;
	}
	# Buscando Pastas
	else {

	    $Paths = array_merge([APP::getCurrentModule(true)], list_paths(APP::getCurrentModule(true)), $Paths);

	    $module = APP::getModules()[APP::getCurrentModule()];

	    $general = is_array(get_config('library')) ? get_config('library') : [];

	    if (is_array($module)) {
		foreach (['library'] as $key => $value) {
		    if (is_array($value)) {
			$general = array_merge($general, $value);
		    } else {
			$general[] = $value;
		    }
		}
	    }

	    foreach ($general as $key => $path) {
		$Paths = array_merge([$path], list_paths($path), $Paths);
	    }

	    APP::setGlobal('get_all_paths', $Paths);

	    return $Paths;
	}
    }

    /**
     * Lista todas as pastas do diretório informado
     * @param string $Pasta
     * @return array
     */
    function list_paths($Pasta) {
	$cache = new Cache('config.paths_' . sha1($Pasta), 1);
	if (!$Pastas = $cache->getContent()) {
	    $Pastas = [];
	    foreach (glob(ABSPATH . "/{$Pasta}/*") as $Path) {
		if (is_dir($Path)) {
		    $Path = str_replace([ABSPATH . '/', ABSPATH . '\\'], null, $Path);
		    $Pastas[] = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $Path);
		    $Pastas = array_merge($Pastas, list_paths($Path));
		}
	    }
	    $cache->setContent($Pastas);
	}
	return $Pastas;
    }

    /**
     * Retorna o tempo de execução da aplicação até o ponto
     * @return float
     */
    function calc_execution_time($decimals = 5) {
	return number_format((microtime(true) - EXECUTION_START) * 1000, $decimals, ',', '.');
    }

    /**
     * 
     * @param mixed $Values
     * @param string $Result
     */
    function json($Values, $Result = 0) {
	exitJson($Values, $Result);
    }

    /**
     * 
     * @param mixed $Value
     * @param string $Result
     */
    function exitJson($Value, $Result = 0) {

	# String
	if (is_string($Value)) {
	    $Value = [
		'result' => $Result,
		'message' => $Value,
	    ];
	}

	# Converteando para array
	if (is_object($Value)) {
	    $values = [];
	    foreach ($Value as $key => $v) {
		$values[$key] = $v;
	    }
	    $Value = $values;
	}

	# Verificando chaves
	if (is_array($Value)) {
	    if (!isset($Value['result'])) {
		$Value['result'] = $Result;
	    }
	    if (!isset($Value['message'])) {
		$Values['message'] = $Result ? 'Success' : 'Fail';
	    }
	}

	# Message
	if (empty($Value['message'])) {
	    $Value['message'] = $Result ? 'Sucesso.' : 'Erros.';
	}

	# Result
	if (empty($Value['result'])) {
	    $Value['result'] = $Result;
	}

	# scape to json
	exit(json_encode($Value));
    }

    /**
     * Criptografa
     * @param string $senha
     * @return string
     */
    function password($senha) {
	return hash('sha512', $senha);
    }

    define('PASSWORD_LENG', 128);

    /**
     * Transforma uma string em um array
     * @param string $String
     * @return array
     */
    function stringToArray($String) {
	if (is_array($String)) {
	    return $String;
	} else if (empty($String)) {
	    return [];
	} else if (preg_match('/^[0-9a-zA-Z_\-]+\=/', $String)) {
	    parse_str($String, $values);
	    return $values;
	} else if (preg_match('/^\[".*?"/', $String) and preg_match_all('/"(.*?)"/', $String, $matches)) {
	    return $matches[1];
	} else if (preg_match_all('/\[([^\[\]]+?)\]/', $String, $busca)) {
	    return $busca[1];
	} else if ($result = json_decode($String)) {
	    return $result;
	} else if (strpos($String, ',') !== false) {
	    return explode(',', $String);
	} else {
	    return [$String];
	}
    }

    /**
     * Converte um array para um string 
     * EX: [value1][value2]
     * @param array $Array
     * @return string
     */
    function arrayToString($Array) {

	if (is_string($Array)) {
	    if (preg_match('/^\[".*?"/', $Array) and preg_match_all('/"(.*?)"/', $Array, $matches)) {
		return arrayToString(array_values($matches[1]));
	    }
	    return $Array;
	} else if (empty($Array)) {
	    return '';
	} else if (is_array($Array)) {
	    // Não possuí só números
	    foreach ($Array as $value) {
		if (preg_match('/[\[\]]/', $value)) {
		    return json_encode($Array);
		    break;
		}
	    }
	    // Somente números
	    return '[' . implode('][', $Array) . ']';
	}
    }

    /**
     * 
     * @param int|array $Total
     * @param int $Page
     * @param int $Forpage
     * @param string $Link
     * @return array [array, total, paginas, pagination]
     */
    function pagination($Total, $Page = 1, $Forpage = 30, $Link = null, $VisiblePages = 11) {

	if ($VisiblePages % 2 == 0) {
	    $VisiblePages --;
	}

	$Paginas = max(1, Number::ceil((is_array($Total) ? count($Total) : $Total) / $Forpage));
	$Page = max(1, min($Page, $Paginas));

	$html = '<nav>';
	if ($Paginas > 1) {
	    $html .= '<ul class="pagination" >';

	    $min = max(1, $Page - ($VisiblePages - 1) * 0.5);
	    $max = min($Paginas, $Page >= (($VisiblePages - 1) * 0.5 + 1) ? $Page + (($VisiblePages - 1) * 0.5) : $Page + 11 - $Page);
	    if ($max == $Paginas) {
		$min = max(1, $Page - $VisiblePages - 1 + ($Paginas - $Page));
	    }

	    # Ir para a primeira página
	    if ($min > 1) {
		$html .= '<li><a data-page="1" href="' . str_replace('#page#', 1, $Link ? $Link : '#') . '" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
	    }

	    # Páginas
	    for ($i = $min; $i <= $max; $i++) {
		$html .= '<li class="' . ($Page == $i ? 'active' : null) . '" ><a data-page="' . $i . '" href="' . str_replace('#page#', $i, $Link ? $Link : '#') . '" >' . $i . '</a></li>';
	    }

	    # Ir para a última página
	    if ($max < $Paginas) {
		$html .= '<li><a href="' . str_replace('#page#', $Paginas, $Link ? $Link : '#') . '" data-page="' . $Paginas . '" aria-label="Next" ><span aria-hidden="true">&raquo;</span></a></li>';
	    }

	    $html .= '</ul>';
	}
	$html .= '</nav>';


	return [
	    'array' => is_array($Total) ? array_slice($Total, ($Page - 1) * $Forpage, $Forpage) : [],
	    'total' => is_array($Total) ? count($Total) : $Total,
	    'pagina' => $Page,
	    'paginas' => $Paginas,
	    'pagination' => $html,
	    'limit' => 'LIMIT ' . $Forpage . ' OFFSET ' . (($Page - 1) * $Forpage),
	];
    }

    /**
     * 
     * @param object $method
     * @param string $obj
     * @param mixed $parameter
     * @return string
     */
    function call_method($method, $obj, $parameter = null) {
	return call_user_func([$obj, $method], $parameter);
    }

    /**
     * 
     * @param string $ModelName
     * @return Model
     */
    function newModel($ModelName) {
	return APP::getInstanceModel(ucfirst($ModelName));
    }

    /**
     * 
     * @param string $ControllerName
     * @return Controller
     */
    function newController($ControllerName) {
	return APP::getInstanceController($ControllerName);
    }

    /**
     * Preenche com zeros a esquerda
     * @param int $int
     * @param int $length
     * @return string
     */
    function zerofill($int, $length = 11) {
	return str_pad((string) $int, $length, '0', 0);
    }

    /**
     * 
     * @param int $nascimento
     * @param int $dataReferencia
     * @return int
     */
    function calc_idade($nascimento, $dataReferencia = __NOW__) {

	$nascimento = !($nascimento = Date::data($nascimento)) ? date('Y-m-d') : $nascimento;
	$dataReferencia = !($dataReferencia = Date::data($dataReferencia)) ? date('Y-m-d') : $dataReferencia;

	$idade = (int) Date::year($dataReferencia) - (int) Date::year($nascimento);
	if (date('md', strtotime($dataReferencia)) < date('md', strtotime($nascimento))) {
	    $idade--;
	}

	return $idade;
    }
    