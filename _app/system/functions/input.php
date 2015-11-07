<?php

    /**
     * Verifica se a variavél está vazia
     * @param mixed $value
     * @return bollean
     */
    function is_empty($value) {
        if (is_string($value)) {
            $value = trim($value);
        }
        return (empty($value) or $value == '0000-00-00' or $value == '00/00/0000' or $value == '00:00:00') ? true : false;
    }

    /**
     * Retorna o valor de um $_POST ou a array $_POST caso o primeiro parametro seja nulo
     * @param type $var_name
     * @param type $filtro
     * @param type $options
     * @return type
     */
    function inputPost($var_name = null, $filtro = 'FILTER_DEFAULT', $options = null) {
        return _filter_input(INPUT_POST, $var_name, $filtro, $options);
    }

    /**
     * Retorna o valor de um $_GET ou a array $_GET caso o primeiro parametro seja nulo
     * @param type $var_name
     * @param type $filtro
     * @param type $options
     * @return type
     */
    function inputGet($var_name = null, $filtro = 'FILTER_DEFAULT', $options = null) {
        return _filter_input(INPUT_GET, $var_name, $filtro, $options);
    }

    /**
     * Retorna o valor de um $_SERVER ou a array $_SERVER caso o primeiro parametro seja nulo
     * @param type $var_name
     * @param type $filtro
     * @param type $options
     * @return type
     */
    function inputServer($var_name = null, $filtro = 'FILTER_DEFAULT', $options = null) {
        return _filter_input(INPUT_SERVER, $var_name, $filtro, $options);
    }

    /**
     * 
     * @param int $type
     * @param string $variable_name
     * @param string|int $filter
     * @param array $options
     */
    function _filter_input($type, $variable_name = null, $filter = 'FILTER_DEFAULT', $options = null) {
        # Fitlro
        if ($filter === null or $filter == 'FILTER_DEFAULT') {
            $filter = FILTER_DEFAULT;
        }
        # Return Array
        if ($variable_name === null) {
            $extraValues = [];
            switch ($type) {
                case INPUT_POST:
                    $extraValues = $_POST;
                    break;
                case INPUT_GET:
                    $extraValues = $_GET;
                    break;
                case INPUT_SERVER:
                    $extraValues = $_SERVER;
                    break;
            }
            return extend($extraValues, (array) filter_input_array($type));
        }
        # Return Value
        else {
            $value = filter_input($type, $variable_name, $filter, $options);
            if ($value === null) {
                switch ($type) {
                    case INPUT_POST:
                        $value = isset($_POST[$variable_name]) ? $_POST[$variable_name] : $value;
                        break;
                    case INPUT_GET:
                        $value = isset($_GET[$variable_name]) ? $_GET[$variable_name] : $value;
                        break;
                    case INPUT_SERVER:
                        $value = isset($_SERVER[$variable_name]) ? $_SERVER[$variable_name] : $value;
                        break;
                }
                return filter_var($value, $filter, $options);
            } else {
                return $value;
            }
        }
    }

    /**
     * Verifica se a variavel está vazia
     * 
     * Contorna as especificações de empty() para 0 e '0'
     * Adicionado verificações para o formato data '0000-00-00' e '00/00/0000'
     * 
     * @param mixed $var
     * @return boolean
     */
    function isEmpty($var) {
        # Exceções para empty
        if (empty($var)) {
            if ($var !== 0 and $var !== '0') {
                return true;
            }
        }
        # Data
        else if (Date::isDate($var)) {
            $value = Date::data($var);
            if ($value == '0000-00-00' or $value === null) {
                return true;
            }
        }
        return false;
    }
    