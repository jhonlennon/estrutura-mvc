<?php

    class GaleriasModel extends Model {

        protected $Table = 'galerias';
        protected $ValueObject = 'GaleriaVO';

        /**
         * 
         * @param array $Parans
         * @param int $CurrentPage
         * @param int $PorPagina
         * @return Pagination
         */
        function Busca(array $Parans = null, $CurrentPage = 1, $PorPagina = 20) {
            $Termos = 'WHERE a.status != 99';
            $Places = [];
            if ($Parans) {
                foreach ($Parans as $key => $value) {
                    if (!isEmpty($value) and ! empty($key)) {
                        switch ($key) {
                            case 'status':
                            case 'ref':
                                $Termos .= " AND a.{$key} = :{$key}";
                                $Places[$key] = $value;
                                break;
                            case 'data':
                                $value = Date::data($value);
                                $Termos .= " AND a.{$key} = :{$key}";
                                $Places[$key] = $value;
                                break;
                        }
                    }
                }
            }
            return $this->ListaPagination($Termos, $Places, $CurrentPage, $PorPagina);
        }

    }
    