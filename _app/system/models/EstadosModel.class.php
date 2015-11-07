<?php

    class EstadosModel extends Model {

        protected $Table = 'estados';
        protected $ValueObject = 'EstadoVO';

        /** @return EstadoVO|null */
        public function getEstado($idUf = null) {
            $busca = $this->Lista('WHERE a.uf = :uf OR a.id = :uf LIMTI 1', ['uf' => $idUf]);
            return count($busca) ? $busca[0] : null;
        }

        /** @return EstadoVO */
        public function getEstados() {
            return $this->Lista('ORDER BY a.title ASC');
        }

        /**
         * select.options
         * @param string $Label uf | title
         * @return string
         */
        public function options($Label = 'title', $Current = null, $optSelecione = true) {
            $html = $optSelecione ? formOption($Label == 'title' ? '-- Selecione --' : '- UF -', '') : '';
            $method = strtolower($Label) == 'title' ? 'getTitle' : 'getUF';
            foreach ($this->getEstados() as $v) {
                $html .= formOption($v->$method(), $v->getId(), in_array($Current, [$v->getId(), $v->getUf()]) ? true : false);
            }
            return $html;
        }

    }
    