<?php
    
    class CidadesModel extends Model {

        protected $Table = 'cidades';
        protected $ValueObject = 'CidadeVO';
        protected $Query = '
            SELECT a.*, estado.uf, estado.title AS estadoTitle
            FROM `#table#` AS a
            INNER JOIN `estados` AS estado ON estado.id = a.estado
            ';
        
        /**
         * Busca complexa
         * @param array $Parans
         * @param int $Pagina
         * @param int $PorPagina
         * @return Pagination
         */
        function busca(array $Parans = null, $Pagina = 1, $PorPagina = 10) {
            $Termos = 'WHERE a.status != 99';
            $Places = [];
            if ($Parans) {
                foreach ($Parans as $key => $value) {
                    if (!isEmpty($value) and ! empty($key)) {
                        switch ($key) {
                            case 'uf':
                                $Termos .= " AND (estado.{$key} = :{$key} OR estado.id = :{$key})";
                                $Places[$key] = $value;
                                break;
                        }
                    }
                }
            }
            return $this->ListaPagination("{$Termos} ORDER BY estado.title ASC, a.title ASC", $Places, $Pagina, $PorPagina);
        }

        /** @return CidadeVO */
        function getCidades($Estado = null) {
            return $this->Lista('WHERE a.estado = :uf OR estado.uf = :uf ORDER BY a.title ASC', ['uf' => $Estado]);
        }

        /** @return CidadeVO */
        function getCidade($id) {
            return $this->getByLabel('id', $id);
        }

    }
    