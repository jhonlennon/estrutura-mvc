<?php

    final class Session {

        private static $_SESSION_;
        private $SessionName;

        /**
         * Inicia uma sessão
         * @param string $sessionName
         */
        public function __construct($sessionName, $ProtectedModule = false) {
            self::start();
            $this->SessionName = ($ProtectedModule ? (is_string($ProtectedModule) ? $ProtectedModule : APP::getCurrentModule()) . '-' : null) . $sessionName;
            if (!isset($_SESSION[self::$_SESSION_][$this->SessionName])) {
                $_SESSION[self::$_SESSION_][$this->SessionName] = null;
            }
        }

        /**
         * Retorna o valor na sessão
         * @param string $name 
         * @param object $defafultValue Valor padrão
         * @return type
         */
        public function get($name = null, $defafultValue = null) {
            if (null == $name) {
                return isset($_SESSION[self::$_SESSION_][$this->SessionName]) ? $_SESSION[self::$_SESSION_][$this->SessionName] : ($_SESSION[self::$_SESSION_][$this->SessionName] = $defafultValue);
            } else {
                return isset($_SESSION[self::$_SESSION_][$this->SessionName][$name]) ? $_SESSION[self::$_SESSION_][$this->SessionName][$name] : ($_SESSION[self::$_SESSION_][$this->SessionName][$name] = $defafultValue);
            }
        }

        /**
         * Seta um valor na sessão
         * @param string $name
         * @param object $value
         */
        public function set($name = null, $value = null) {
            if (null == $name) {
                return $_SESSION[self::$_SESSION_][$this->SessionName] = $value;
            } else {
                return $_SESSION[self::$_SESSION_][$this->SessionName][$name] = $value;
            }
        }

        /**
         * Inicia a sessão no php
         */
        public static function start($Module = null) {
            if (!session_id()) {

                $config = get_config('project');
                
                self::$_SESSION_ = $config['name'];

                $path = ABSPATH . '/temp/session';
                
                if(!file_exists($path)){
                    mkdir($path, 0777);
                    chmod($path, 0777);
                }
                
                if ($Module) {
                    $path .= '/' . $Module;
                    if (!file_exists($path)) {
                        mkdir($path, 0777);
                        chmod($path, 0777);
                    }
                }

                session_cache_expire($config['sessiontime']);
                session_save_path($path);

                session_start();

                # Iniciando array
                if (!isset($_SESSION[self::$_SESSION_])) {
                    $_SESSION[self::$_SESSION_] = [];
                }
            }
        }

        /**
         * Retorna a chave da sessão
         * @return type
         */
        public static function getId() {
            self::start();
            return session_id();
        }

        /**
         * Destroi a sessão
         */
        public function destroy() {
            if (isset($_SESSION[self::$_SESSION_][$this->SessionName])) {
                unset($_SESSION[self::$_SESSION_][$this->SessionName]);
            }
        }

    }
    