<?php

    class BannersModel extends Model {

	protected $Table = 'banners';
	protected $ValueObject = 'BannerVO';

	/**
	 * Efetua uma busca complexa a partir dos parâmetros passados
	 * @param array $Paranmetros
	 * @param int $CurrentPage
	 * @param int $PorPagina
	 * @param string $OrderBy
	 * @return Pagination
	 */
	function Busca(array $Paranmetros = null, $CurrentPage = 1, $PorPagina = 10, $OrderBy = 'rand()') {
	    $Termos = 'WHERE a.status != 99';
	    $Places = [];
	    if ($Paranmetros) {
		foreach ($Paranmetros as $key => $value) {
		    if (!isEmpty($value) and ! empty($key)) {
			switch ($key) {
			    case 'data':
				$Termos .= " AND ((a.inicio = a.fim  AND a.inicio = '0000-00-00') OR (:data BETWEEN a.inicio AND a.fim))";
				$Places['data'] = Date::data($value);
				break;
			    case 'dia':
				$Termos .= ' AND (a.dias LIKE "%[*]%" OR a.dias LIKE CONCAT("%[",:dia,"]%"))';
				$Places['dia'] = $value;
				break;
			    case 'status':
			    case 'ref':
				$Termos .= " AND a.{$key} = :{$key}";
				$Places[$key] = $value;
				break;
			    case 'title':
				$Termos .= ' AND (a.title CONCAT("%",:title,"%"))';
				$Places['title'] = $value;
				break;
			}
		    }
		}
	    }
	    return $this->ListaPagination("{$Termos} ORDER BY {$OrderBy}", $Places, $CurrentPage, $PorPagina);
	}

    }
    