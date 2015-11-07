<?php

    class Form {

        private $Attr;
        private $Html = '';
        private $linesCount = -1;
        private $colunsCount = 0;

        /**
         * Cria um formulário
         * @param string $action
         * @param string $class
         * @param array $initValues
         * @param array $Atributos
         * @param string $method post || get
         * @param string $enctype 
         */
        public function __construct($action = null, $class = null, $initValues = null, array $Atributos = null, $method = 'post', $enctype = 'multipart/form-data') {
            if ($Atributos) {
                $this->Attr = $Atributos;
            } else {
                $this->Attr = [];
            }
            $this->Attr['action'] = $action != null ? $action : Input::SERVER('REQUEST_URI');
            $this->Attr['enctype'] = $enctype;
            $this->Attr['method'] = $method;
            $this->Attr['class'] = trim($class . ' ajax');
            $this->Attr['data-setvalues'] = $initValues;
        }

        /**
         * Retorna um textarea
         * @param string $name
         * @param string $value
         * @param array $atribrutos
         * @return string
         */
        public static function textArea($name, $value = null, array $atribrutos = null) {
            $atribrutos['name'] = $name;
            return '<textarea ' . self::Attributes($atribrutos) . ' >' . $value . '</textarea>';
        }

        /**
         * Cria e retorna um form.input
         * @param type $name
         * @param type $type
         * @param type $value
         * @param type $Atributos
         * @return type
         */
        public static function input($name, $type = 'text', $value = null, array $Atributos = null) {
            $Atributos['name'] = $name;
            $Atributos['type'] = $type;
            $Atributos['class'] = trim(@$Atributos['class'] . ' form-control');
            if ($value)
                $Atributos['value'] = $value;
            return '<input ' . self::Attributes($Atributos) . ' />';
        }

        /**
         * Retorna um form.input.number
         * @param string $name
         * @param array $Atributos
         * @return string
         */
        public static function inputNumber($name, $Atributos = null) {
            return self::input($name, 'number', null, $Atributos);
        }

        /**
         * Retorna um form.input.hidden
         * @param string $name
         * @param array $Atributos
         * @return string
         */
        public static function inputHidden($name, array $Atributos = null) {
            return self::input($name, 'hidden', null, $Atributos);
        }

        /**
         * Retorna um form.input.password
         * @param string $name
         * @param array $Atributos
         * @return string
         */
        public static function inputPassword($name, array $Atributos = null) {
            $Atributos['autocomplete'] = 'off';
            return self::input($name, 'password', null, $Atributos);
        }

        /**
         * Retorna um form.input.text
         * @param string $name
         * @param array $Atributos
         * @return string
         */
        public static function inputText($name, array $Atributos = null) {
            return self::input($name, 'text', null, $Atributos);
        }

        /**
         * Retorna um form.input.date
         * @param string $name
         * @param array $Atributos
         * @return string
         */
        public static function inputDate($name, array $Atributos = null) {
            return self::input($name, 'date', null, $Atributos);
        }

        /**
         * Retorna um form.input.color
         * @param string $name
         * @param array $Atributos
         * @return string
         */
        public static function inputColor($name, array $Atributos = null) {
            return self::input($name, 'color', null, $Atributos);
        }

        /**
         * Retorna um form.input.email
         * @param type $name
         * @param array $Atributos
         * @return type
         */
        public static function inputEmail($name, array $Atributos = null) {
            return self::input($name, 'email', null, $Atributos);
        }

        /**
         * Retorna um form.select
         * @param string $name
         * @param string|array $options
         * @param array $Atributos
         * @return string
         */
        public static function select($name, $options = null, array $Atributos = null) {
            $Atributos['name'] = $name;
            $Atributos['class'] = @$Atributos['class'] . ' form-control';
            if (is_string($options)) {
                $opts = $options;
            } else if (is_array($options)) {
                if (preg_match('/<option .*?>/', current($options))) {
                    foreach ($options as $opt) {
                        $opts .= $opt;
                    }
                } else {
                    $opts = '';
                    foreach ($options as $key => $opt) {
                        $opts .= '<option value="' . $key . '" >' . $opt . '</option>';
                    }
                }
            } else {
                $opts = null;
            }
            return '<select ' . self::Attributes($Atributos) . ' >'
                    . $opts
                    . '</select>';
        }

        /**
         * Adiciona uma label ao formulário
         * @param type $caption
         * @param type $html
         * @param type $colunas
         * @param array $Atributos
         */
        public function label($caption, $html, $colunas = 12, array $Atributos = null) {
            $this->addCollun(formLabel($caption, $html, $Atributos));
        }

        /**
         * Adiciona conteúdo ao fomulário dentro de uma coluna na linha atual
         * @param string $html
         * @param int $colunas 1 à 12
         * @param arra $Atributos
         */
        public function addCollun($html, $colunas = 12, array $Atributos = null) {
            if ($this->linesCount == -1)
                $this->addRow();
            $this->colunsCount += $colunas;
            if ($this->colunsCount > 12) {
                $this->addRow();
                $this->colunsCount = $colunas;
            }
            if (isset($Atributos['class'])) {
                $Atributos['class'] .= ' col-xs-' . $colunas;
            } else {
                $Atributos['class'] = 'col-xs-' . $colunas;
            }
            $this->addContent('<div ' . self::Attributes($Atributos) . ' >' . $html . '</div>');
        }

        /**
         * Abre uma nova linha
         */
        public function addRow() {
            if ($this->linesCount >= 0)
                $this->addContent('</div>');
            $this->linesCount ++;
            $this->colunsCount = 0;
            $this->addContent('<div class="row" >');
        }

        /**
         * Retorna os atributos formatados
         * @param array $attr
         * @return type
         */
        public static function Attributes(array $attr = null) {
            return Utils::Attributes($attr);
        }

        /**
         * 
         * @param string $caption
         * @param string $class
         * @param string $type
         * @param array $Atributos
         * @return string
         */
        public static function button($caption, $class = null, $type = 'submit', array $Atributos = null) {
            $Atributos['class'] = $class;
            return '<button type="' . $type . '" ' . self::Attributes($Atributos) . ' >' . $caption . '</button>';
        }

        /**
         * Adiciona conteúdo ao formulário
         * @param string $Html
         */
        public function addContent($Html) {
            $this->Html .= $Html;
        }

        /**
         * Retorna o HTML do formulário
         */
        public function display() {
            #Header
            $html = '<form ' . self::Attributes($this->Attr) . ' >';

            //Fields
            $html .= $this->Html . '</div>';
            //end: fields
            #End
            return $html . '</form>';
        }

    }
    