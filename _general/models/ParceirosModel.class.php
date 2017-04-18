<?php

    class ParceirosModel extends Model {

	protected $Table = 'parceiros';
	protected $ValueObject = 'ParceiroVO';

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
				$Termos .= " AND a.{$key} = :{$key}";
				$Places[$key] = $value;
				break;
			}
		    }
		}
	    }
	    return $this->ListaPagination("{$Termos} ORDER BY a.nome ASC", $Places, $CurrentPage, $PorPagina);
	}

    }
    