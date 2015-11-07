<?php

    class PaginasModel extends Model {

        protected $Table = 'paginas';
        protected $ValueObject = 'PaginaVO';

        /**
         * Busca complexa
         * @param array $Parans
         * @param int $Pagina
         * @param int $PorPagina
         * @return Pagination
         */
        function Busca(array $Parans = null, $Pagina = 1, $PorPagina = 10) {
            $Termos = 'WHERE a.status!=99';
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
                            case 'search':
                                $por = "LIKE CONCAT('%',:{$key},'%')";
                                $Termos .= " AND (a.title {$por} OR a.descricao {$por} OR a.keywords {$por})";
                                $Places[$key] = $value;
                                break;
                        }
                    }
                }
            }
            return $this->ListaPagination("{$Termos} ORDER BY a.ordem ASC", $Places, $Pagina, $PorPagina);
        }

        /**
         * Organiza pela referência
         * @param string $Ref
         */
        function organizaRef($Ref) {
            foreach ($this->Lista('WHERE a.ref = :ref AND a.status != 99 ORDER BY a.ordem ASC, a.title ASC', ['ref' => $Ref]) as $i => $v) {
                $v->setOrdem($i);
                $v->Save();
            }
        }
        
        /**
         * Retorna a última posição
         * @param string $Ref
         * @return int
         */
        function lastOrdem($Ref) {
            foreach ($this->Lista('WHERE a.ref = :ref AND a.status != 99 ORDER BY a.ordem DESC LIMIT 1', ['ref' => $Ref]) as $v) {
                return $v->getOrdem() + 1;
            }
            return 1;
        }

        /**
         * 
         * @param string $Ref
         * @param boolean $AutoCrete
         * @return PaginaVO
         */
        function getByRef($Ref, $AutoCrete = false) {
            if ($Ref) {
                $pagina = $this->getByLabel('ref', $Ref);
                if (!$pagina and $AutoCrete) {
                    $pagina = $this->newValueObject();
                    $pagina->setRef($Ref);
                    $this->Save($pagina);
                }
                return $pagina;
            }
            return null;
        }

    }
    