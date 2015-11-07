<?php

    /**
     * Check.class [ HELPPER ]
     * 
     * 
     * @link www.lifeweb.com.br LifeWeb
     * @author Jhon Lennon S. almeida <jhonlennon21@gmail.com>
     * @copyright (c) 2014, Jhon Lennon S. Almeida LifeWeb Soluções para Web e Arte Gráfica
     */
    class Check {

        /**
         * URl principal do site.
         * @var string
         */
        private static $Host = WB_HOST;
        private static $URLValues;

        /**
         * Transforma uma string em urlAmigavel e retorna o resultado.
         * @param string $Value
         * @return string
         */
        public static function url($Value) {

            if (is_array($Value)) {
                $values = $Value;
            } else {
                $values = func_get_args();
            }

            foreach ($values as $key => $value) {
                if (is_null($value)) {
                    $values[$key] = self::getValue($key);
                }
            }

            $result = '';
            foreach ($values as $value) {
                $result .= '/';
                $result .= self::formatUrl($value);
            }
            return self::$Host . $result;
        }

        public static function getCurrentUrl() {
            return self::url(GVARS);
        }

        /**
         * Pega um valor da url
         * @param string|int $keyName
         * @return string
         */
        public static function getValue($keyName = null) {
            if (!self::$URLValues) {
                self::$URLValues = $GVARS = explode('/', GVARS);
                self::$URLValues['page'] = current($GVARS);
                for ($i = 0; $i < count($GVARS); $i++) {
                    self::$URLValues[$i] = $GVARS[$i];
                    if (!isset(self::$URLValues[$GVARS[$i]])) {
                        self::$URLValues[$GVARS[$i]] = isset($GVARS[$i + 1]) ? strtolower($GVARS[$i + 1]) : null;
                    }
                }
            }
            if (is_null($keyName)) {
                return self::$URLValues;
            }
            return isset(self::$URLValues[$keyName]) ? strtolower(self::$URLValues[$keyName]) : null;
        }

        /**
         * Formata um valor para url
         * @param string $Value
         * @return string
         */
        public static function formatUrl($Value) {
            $format = array();
            $format['a'] = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
            $format['b'] = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';
            $data = strtr(utf8_decode($Value), utf8_decode($format['a']), $format['b']);
            $data = strip_tags(trim($data));
            $data = str_replace(' ', '-', $data);
            $data = preg_replace('/[\-]{2,}/ui', '-', $data);
            return strtolower(utf8_encode($data));
        }

    }
    