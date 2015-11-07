<?php

    class OptionsModel extends Model {

	protected $Table = 'options';
	protected $ValueObject = 'OptionVO';

	/**
	 * Reorganiza a Ordem pela referência
	 * @param type $Ref
	 * @return boolean
	 */
	function organizaOrdemReferencia($Ref) {
	    foreach ($this->listByRef($Ref) as $i => $v) {
		$this->_Update($this->Table, ['ordem' => (int) $i + 1], 'WHERE `id` = :id LIMIT 1', ['id' => (int) $v->getId()]);
	    }
	    return true;
	}

	/**
	 * Retorna o título pelo ID
	 * @param int $ID
	 * @return string|null
	 */
	function titleById($ID) {
	    $vo = $this->getByLabel('id', $ID);
	    return $vo ? $vo->getTitle() : null;
	}

	/**
	 * Last Ordem By Ref
	 * @param string $Ref
	 * @return int
	 */
	function getLastOrdem($Ref) {
	    foreach ($this->Lista('WHERE a.ref = :ref ORDER BY a.ordem DESC LIMIT 1', ['ref' => $Ref]) as $v) {
		return $v->getOrdem() + 1;
	    }
	    return 1;
	}

	/**
	 * Lista pela referência
	 * @param string $Ref
	 * @param string $Ordem
	 * @return OptionVO
	 */
	function listByRef($Ref, $Status = 1, $OrderBY = null) {
	    return $this->Lista('WHERE a.ref = :ref AND (:status IS NULL OR a.status = :status) ORDER BY ' . ($OrderBY ? $OrderBY : 'a.ordem ASC, a.title ASC'), ['ref' => $Ref, 'status' => $Status], false, true);
	}

	/**
	 * Retorna a lista de options para select
	 * @param string $Ref
	 * @param string $Ordem
	 * @param int $setValue
	 * @return string HTML
	 */
	function optionsByRef($Ref = null, $OrderBY = null, $setValue = 0) {
	    $html = '';
	    foreach ($this->listByRef($Ref, 1, $OrderBY) as $v) {
		$html .= formOption($v->getTitle(), $v->getId(), $v->getId() == $setValue);
	    }
	    return $html;
	}

	/**
	 * 
	 * @param string $Ref
	 * @param string $OrderBY
	 * @param int $setValue
	 * @return string HTML
	 */
	function options($Ref = null, $OrderBY = null, $setValue = 0) {
	    return $this->optionsByRef($Ref, $OrderBY, $setValue);
	}

    }
    