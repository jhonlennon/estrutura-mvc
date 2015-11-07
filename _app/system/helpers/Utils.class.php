<?php

    class Utils {

	/**
	 * Criptografia de senha
	 * @param string $value
	 * @return string 128 caracteres
	 */
	public static function encryptPassword($value) {
	    return hash('sha512', $value);
	}
	
	/**
	 * Calcula a idade em anos
	 * @param string $Nascimento
	 * @param string $DataReferencia
	 * @return int
	 */
	public static function idade($Nascimento, $DataReferencia = 'NOW') {
	    $inicio = Date::data($Nascimento);
	    $fim = $DataReferencia == 'NOW' ? date('Y-m-d') : Date::data($DataReferencia);
	    $anos = Date::year($fim) - Date::year($inicio);
	    return $anos;
	}

	/**
	 * Corta a string no número de palavras especificados
	 * @param string $String
	 * @param int $Words
	 * @return string
	 */
	public static function getWords($String, $Words = 10) {
	    return preg_replace('/(([^ ,\.]+[ ,\.]){1,' . (int) $Words . '}).*/', '$1', trim(strip_tags($String)));
	}

	/**
	 * Retorna o valor se a condição for verdadeira
	 * @param boolean $condicao
	 * @param object $return
	 * @return objcet $return
	 */
	public static function returnIf($condicao, $return = null) {
	    return $condicao ? $return : null;
	}

	/**
	 * Formata os atributos para TAG html
	 * @param array $Atributos
	 */
	public static function Attributes(array $Atributos = null) {
	    if ($Atributos and ! empty($Atributos)) {
		$html = '';
		foreach ($Atributos as $key => $value) {
		    if ($value != null and ! is_object($value)) {
			$aspas = '"';
			if (is_array($value)) {
			    $value = htmlspecialchars(json_encode($value));
			} else {
			    $value = htmlspecialchars($value);
			}
			$html .= "{$key}={$aspas}{$value}{$aspas} ";
		    }
		}
		return substr($html, 0, -1);
	    }
	    return null;
	}

	/**
	 * Redimentsiona uma imagem
	 * @param int $w
	 * @param int $h
	 * @param string $arquivo
	 * @param string $resizeType
	 * @param int $qualidade
	 * @param uint $color
	 * @return boolean
	 */
	static function redimensionaImg($w, $h, $pasta, $arquivo, $resizeType = 'crop', $qualidade = 100, $color = '#FFFFFF', $pastaResize = 'miniatura') {

	    $arquivo = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $arquivo);
	    $pastaArquivo = dirname($arquivo);

	    if (in_array($pastaArquivo, ['.', '/', '\\']) or ! $pastaArquivo) {
		$pastaArquivo = '';
	    } else {
		$pastaArquivo .= DIRECTORY_SEPARATOR;
	    }

	    $arquivo = basename($arquivo);
	    $pasta = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $pasta) . DIRECTORY_SEPARATOR;

	    $fullDir = "{$pasta}{$pastaArquivo}{$arquivo}";

	    if (is_dir($fullDir)) {
		$arquivo = 'default.jpg';
		$resizeType = 'preenchimento';
	    } else if (!file_exists($fullDir)) {
		$arquivo = "default.jpg";
		$resizeType = 'preenchimento';
	    }

	    # Arquivo default.jpg
	    if ($arquivo == 'default.jpg' and ! file_exists("{$pasta}{$pastaArquivo}default.jpg")) {
		trigger_error('O Arquivo default.jpg não foi criado na pasta de imagens.');
	    }

	    $fullDir = "{$pasta}{$pastaArquivo}{$arquivo}";

	    if (file_exists($fullDir)) {

		$extension = self::getFileExtension($arquivo);

		if (!file_exists("{$pasta}{$pastaArquivo}{$pastaResize}")) {
		    mkdir("{$pasta}{$pastaArquivo}{$pastaResize}");
		}


		$fileName = preg_replace("/(.*)(\..*)/", "$1-" . sha1(str_replace('#', '', strtolower("{$w}-{$h}-{$qualidade}-{$resizeType}-{$color}"))) . "$2", basename($arquivo));
		$fileName = $pastaArquivo . $pastaResize . DIRECTORY_SEPARATOR . $fileName;
		$newFile = "{$pasta}{$fileName}";

		if (!file_exists($newFile)) {
		    $img = new IMGCanvas($fullDir);
		    $img
			    ->hexa($color)
			    ->redimensiona($w, $h, $resizeType)
			    ->grava($newFile, $qualidade);
		}

		return base_url_images(str_replace('\\', '/', $fileName));
	    }

	    return null;
	}

	/**
	 * Retorna a extenção de um arquivo
	 * @param string $name
	 * @return string|null
	 */
	static function getFileExtension($name) {
	    $extension = preg_replace('/^.*\./', null, (string) $name);
	    return (empty($extension) or strlen($extension) > 4) ? null : strtolower($extension);
	}

	/**
	 * Gerá um Código
	 * @param int $tamanho
	 * @param boolean $minusculas
	 * @param boolean $maiusculas
	 * @param boolean $numeros
	 * @param boolean $simbolos
	 * @return string
	 */
	static function gerarCodigo($tamanho = 8, $minusculas = true, $maiusculas = false, $numeros = true, $simbolos = false) {

	    $simb = '!@#$%*';
	    $retorno = '';
	    $caracteres = '';

	    if ($minusculas) {
		$caracteres .= implode('', range('a', 'z'));
	    }
	    if ($maiusculas) {
		$caracteres .= implode('', range('A', 'Z'));
	    }
	    if ($numeros) {
		$caracteres .= implode('', range(0, 9));
	    }
	    if ($simbolos) {
		$caracteres .= $simb;
	    }

	    $len = strlen($caracteres);

	    for ($n = 1; $n <= $tamanho; $n++) {
		$rand = mt_rand(1, $len);
		$retorno .= $caracteres[$rand - 1];
	    }

	    return $retorno;
	}

    }
    