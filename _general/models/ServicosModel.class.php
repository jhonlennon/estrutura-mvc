<?php

    class ServicosModel extends Model {

	protected $Table = 'servicos';
	protected $ValueObject = 'ServicoVO';

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
	    return $this->ListaPagination("{$Termos} ORDER BY a.title ASC", $Places, $CurrentPage, $PorPagina);
	}

    }
    