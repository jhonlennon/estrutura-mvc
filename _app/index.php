<?php

    ob_start();


    if (realpath($_SERVER['SCRIPT_FILENAME']) == __FILE__) {
        foreach (['public_html', 'html', 'public'] as $path) {
            if (file_exists($path)) {
                header('Location: ' . $path);
            }
        }
        exit('Impossível acessar diretamente.');
    }

    include __DIR__ . '/system/boot.inc.php';
    include __DIR__ . '/system/core/APP.class.php';
    include __DIR__ . '/system/libraries/Cache.class.php';

    # Funções do Framework
    foreach (array_merge(glob(ABSPATH . '/system/functions/*.php'), glob(ABSPATH . '/General/functions/*.php')) as $file) {
        include_once $file;
    }

    /**
     * Busca o arquivos pelas pastas da aplicação
     * @param string $filename
     * @return boolean|string
     */
    function _file_exists($filename, $log = true) {
        foreach (get_all_paths() as $path) {
            if (file_exists($source = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ABSPATH . "/{$path}/{$filename}"))) {
                return $source;
            }
        }
        if ($log) {
            error_log('Arquivo ' . $filename . ' não existem na aplicação.');
        }
        return false;
    }

    /**
     * Carrega os arquivos
     * @param string $Class
     * @return boolean
     */
    function __autoload($Class) {
        
	if ($source = _file_exists("{$Class}.class.php")) {
            include_once $source;
            return true;
        }
        
	trigger_error('A class `' . $Class . '` não existe.');
	
        return false;
    }
    