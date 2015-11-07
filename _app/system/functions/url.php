<?php

    function location($url) {
	ob_clean();
	header('Location: ' . $url);
	exit;
    }

    /**
     * Retorna url formatada
     * @param type $Controller_Action
     * @param array $Variables
     * @param type $Modulo
     * @param array $VariaveisGet
     * @param boolean $secure
     * @return string
     */
    function url($Controller_Action = '', array $Variables = null, $Modulo = null, array $VariaveisGet = null, $secure = false) {

	# Base
	$url = base_url();

	# Modulo
	if ($Modulo === null) {
	    $Modulo = APP::getCurrentModule();
	}
	if ($Modulo != APP::getDefaultModule() or ( $Controller_Action and APP::getModules(explode('/', $Controller_Action)[0]))) {
	    $url .= '/' . $Modulo;
	}

	# Controller/Action
	if (!empty($Controller_Action)) {
	    if ($Variables) {
		if (count($ex = explode('/', $Controller_Action)) == 1) {
		    if (controller_exists($ex[0])) {
			$url .= "/{$ex[0]}/index";
		    } else {
			$url .= '/index/' . $ex[0];
		    }
		} else {
		    $url .= '/' . $Controller_Action;
		}
	    } else {
		$url .= '/' . $Controller_Action;
	    }
	} else if ($Variables) {
	    $url .= 'index/index';
	}


	# Variaveis
	if ($Variables) {
	    # Chaves númericas
	    if (key($Variables) === 0) {
		foreach ($Variables as $key => $value) {
		    $url .= '/' . url_paranformat($value);
		}
	    }
	    # Chaves nomeadas
	    else {
		foreach ($Variables as $key => $value) {
		    $url .= '/' . url_paranformat($key) . '/' . url_paranformat($value);
		}
	    }
	}

	# Retornando a URL
	if ($VariaveisGet) {
	    $url .= '?' . http_build_query($VariaveisGet);
	}

	# HTTPS
	if ($secure) {
	    $url = str_replace('http:', 'https:', $url);
	}

	return $url;
    }

    /**
     * Formata o valor
     * @param string $Value
     * @return string
     */
    function url_paranformat($Value) {
	$format = array();
	$format['a'] = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()-+={[}]/?;:.,\\\'<>°ºª';
	$format['b'] = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                ';
	$data = strtr(utf8_decode((string) $Value), utf8_decode($format['a']), $format['b']);
	$data = strip_tags(trim($data));
	$data = str_replace(' ', '-', $data);
	$data = preg_replace('/[\-]{2,}/ui', '-', $data);
	return strtolower(utf8_encode($data));
    }

    /**
     * Codifica uma array para url
     * @param array $values
     * @return string
     */
    function url_encode(array $values) {
	return http_build_query($values);
    }

    /**
     * Decodifca um string de valor e retorna um array
     * @param string $str
     * @return array
     */
    function url_decode($str) {
	parse_str($str, $output);
	return $output;
    }

    /**
     * Retorna os parâmetros da URL
     * @param string $key
     * @param mixed $default Retorno padrão caso o chave não exista
     * @return string
     */
    function url_parans($key = null, $default = null) {
	$parans = APP::getParans();
	if ($key !== null) {
	    return isset($parans[$key]) ? $parans[$key] : $default;
	} else {
	    return $parans;
	}
    }

    /**
     * Retorna a url base
     * @return string
     */
    function base_url() {
	return APP::getBaseUrl();
    }

    /**
     * Retorna o diretório da imagem
     * @param string $imageName
     * @return string
     */
    function base_url_images($imageName = null) {
	return base_url() . '/' . get_config()['upload']['imagens'] . '/' . $imageName;
    }

    /**
     * Retorna o diretório url do arquivo
     * @param string $filename
     * @return string
     */
    function source($filename = '') {
	return preg_replace('/\/$/', null, str_replace('\\', '/', base_url() . str_replace('//', '/', '/' . $filename)));
    }

    /**
     * Retorna o diretório url do arquivo na pasta de arquivo enviados
     * @param type $filename
     * @return type
     */
    function source_files($filename = '') {
	return source(get_config()['upload']['arquivos'] . '/' . $filename);
    }

    /**
     * Retorna o diretório url do arquivo na pasta de arquivo enviados
     * @param type $filename
     * @return type
     */
    function source_images($filename = '') {
	return source(get_config()['upload']['imagens'] . '/' . $filename);
    }

    /**
     * Retorna o diretório absoluto dos arquivos públicos
     * @param string $filename
     * @return string
     */
    function abs_source($filename = null) {
	$source = ABSPATH . '/' . APP::getPublicPath() . '/' . $filename;
	$source = str_replace(['//', '/', '\\'], DIRECTORY_SEPARATOR, $source);
	$source = preg_replace('/[\/\\\]$/', null, $source);
	return $source;
    }

    /**
     * Retorna o diretório absoluto da pasta de arquivos
     * @param string $filename
     * @return string
     */
    function abs_source_files($filename = null) {
	return abs_source(get_config()['upload']['arquivos'] . '/' . $filename);
    }

    /**
     * Retorna o diretório absoluto de uma imagem
     * @param string $filename
     * @return string
     */
    function abs_source_images($filename = null) {
	return abs_source(get_config()['upload']['imagens'] . '/' . $filename);
    }
    