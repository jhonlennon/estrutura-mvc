<?php

    class Pagination {

	private $PorPagina = 20;
	private $Count = 0;
	private $VisiblePages = 11;
	private $Registros;
	private $CurrentPage = 1;
	private $TotalPaginas = 1;

	/**
	 * Informa a página atual
	 * @param type $CurrentPage
	 * @return \Pagination
	 */
	function setCurrentPage($CurrentPage) {
	    $this->CurrentPage = max(1, $CurrentPage);
	    $this->checkCurrentPage();
	    return $this;
	}

	function getCurrentPage() {
	    return $this->CurrentPage;
	}

	function getForPage() {
	    return $this->PorPagina;
	}

	/**
	 * Número total de registros
	 * @param int $Count
	 * @return \Pagination
	 */
	function setCount($Count) {
	    $this->Count = (int) $Count;
	    $this->checkCurrentPage();
	    return $this;
	}

	/**
	 * Retorna o número total de registros
	 * @return int
	 */
	function getCount() {
	    return $this->Count;
	}

	/**
	 * Informa o número de registros por página
	 * @param int $PorPagina
	 * @return \Pagination
	 */
	function setPorPagina($PorPagina) {
	    $this->PorPagina = max(1, $PorPagina);
	    $this->checkCurrentPage();
	    return $this;
	}

	/**
	 * Verifica a página atual se está dentro dos limites
	 * @return \Pagination
	 */
	private function checkCurrentPage() {
	    $this->TotalPaginas = Number::ceil(max($this->Count, 1) / $this->PorPagina);
	    $this->CurrentPage = max(1, min($this->CurrentPage, $this->TotalPaginas));
	    return $this;
	}

	/**
	 * 
	 * @param array $Registros
	 * @param boolean $Fatiar
	 * @return \Pagination
	 */
	function setRegistros(array $Registros, $Fatiar = false) {
	    if ($Fatiar) {
		$this->setCount(count($Registros));
		$this->Registros = array_slice($Registros, ($this->CurrentPage - 1) * $this->PorPagina, $this->PorPagina);
	    } else {
		$this->Registros = $Registros;
	    }
	    return $this;
	}

	/**
	 * Retorna os registros passado para o object já cortado
	 * @return array|ValueObject
	 */
	function getRegistros() {
	    return $this->Registros;
	}

	/**
	 * Informa o número máximo de páginas visivel durante a navegação
	 * @param int $VisiblePages Precisa ser um número impar
	 * @return \Pagination
	 */
	public function setVisiblePages($VisiblePages) {
	    $this->VisiblePages = max(3, $VisiblePages - ($VisiblePages % 2 == 1 ? 0 : 1));
	    return $this;
	}

	/**
	 * Retorna o LIMIT e OFFSET para buscas MySql
	 * @return string ex: LIMIT 10 OFFSET 30
	 */
	public function getLimitOffset() {
	    return 'LIMIT ' . $this->PorPagina . ' OFFSET ' . (($this->CurrentPage - 1) * $this->PorPagina);
	}

	/**
	 * 
	 * @return string
	 */
	public function __toString() {
	    return $this->display();
	}

	/**
	 * Retorna o HTML da paginação
	 * @param string $Link
	 * @param array $Attributes
	 * @return string
	 */
	function display($Link = null, array $Attributes = null) {
	    if ($this->Count) {
		$html = '<nav ' . htmlAttributes($Attributes) . ' >';

		if ($this->TotalPaginas > 1) {
		    $html .= '<ul class="pagination" >';

		    # Min e Max
		    $min = (int) max(1, $this->CurrentPage - ($this->VisiblePages - 1) * 0.5);
		    $max = (int) min($this->TotalPaginas, $this->CurrentPage >= (($this->VisiblePages - 1) * 0.5 + 1) ? $this->CurrentPage + (($this->VisiblePages - 1) * 0.5) : $this->CurrentPage + 11 - $this->CurrentPage);
		    if ($this->TotalPaginas == $max) {
			$min = max(1, $max - $this->VisiblePages + 1);
		    }


		    # Ir para a primeira página
		    if ($min > 1) {
			$html .= '<li><a data-page="1" href="' . str_replace('#page#', 1, $Link ? $Link : '#') . '" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
		    }

		    # Páginas
		    for ($i = $min; $i <= $max; $i++) {
			$html .= '<li class="' . ($this->CurrentPage == $i ? 'active' : null) . '" ><a data-page="' . $i . '" href="' . str_replace('#page#', $i, $Link ? $Link : '#') . '" >' . $i . '</a></li>';
		    }

		    # Ir para a última página
		    if ($max < $this->TotalPaginas) {
			$html .= '<li><a href="' . str_replace('#page#', $this->TotalPaginas, $Link ? $Link : '#') . '" data-page="' . $this->TotalPaginas . '" aria-label="Next" ><span aria-hidden="true">&raquo;</span></a></li>';
		    }

		    $html .= '</ul>';

		    return $html . '</nav> <!-- ' . calc_execution_time() . 'ms -->';
		}
	    }

	    return '';
	}

    }
    