<?php

    /** Microtime ao iniciar aplicação */
    define('EXECUTION_START', microtime(true));

    # Timezone
    date_default_timezone_set('America/Sao_Paulo');
    
    /** Form[enctype=multipart/form-data] */
    define('FORM_ENCTYPE', 'multipart/form-data');

    /** Timestamp */
    define('__NOW__', date('Y-m-d H:i:s'));

    /** CHARSET da página */
    define('CHARSET', 'UTF-8');

    define('LANG', 'pt_BR');

    /** Raiz dos arquivos da aplicação */
    define('ABSPATH', dirname(__DIR__));

    /** @var boolean Verifica se a conexão é AJAX */
    define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

    /** @var boolean Verifica se a valores $_POST */
    define('IS_POST', isset($_POST) and is_array($_POST) and count($_POST));

    /** Verifica se está trabalhando em localhost */
    define('IS_LOCAL', $_SERVER['SERVER_NAME'] == 'localhost' ? true : false);

    /** Verifica se está trabalhando em servidor externo */
    define('IS_EXTERNAL', !IS_LOCAL);

    header("Content-Type: text/html; charset=" . CHARSET, true);
    ini_set('mbstring.internal_encoding', CHARSET);
    error_reporting(E_ALL);
    ini_set("display_errors", IS_LOCAL ? 1 : 0);
    ini_set('log_errors', 1);
    ini_set('error_log', ABSPATH . '/temp/errors.log');

    /** Unidades de medidas  */
    define('KB', 1024);
    define('MB', 1048576);
    define('GB', 1073741824);
    define('TB', 1099511627776);

    # Pastas de Include
    $__INC_PATHS__ = [
        # Absolute
        '',
        # System
        'system/helpers',
        'system/core',
        'system/crud',
        'system/libraries',
        'system/models',
        'system/valueobject',
    ];

    # Arquivos Temporários
    if (!file_exists(ABSPATH . '/temp')) {
        mkdir(ABSPATH . '/temp');
        chmod(ABSPATH . '/temp', 0777);
        mkdir(ABSPATH . '/temp/cache');
        chmod(ABSPATH . '/temp/cache', 0777);
        mkdir(ABSPATH . '/temp/session');
        chmod(ABSPATH . '/temp/session', 0777);
    }