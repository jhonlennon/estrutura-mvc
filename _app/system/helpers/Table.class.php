<?php

    class Table {

	public $xhtml = true; // for col tags
	private $thead = [];
	private $tfoot = [];
	private $tbody_ar = [];
	private $cur_section;
	private $colgroups_ar = [];
	private $cols_ar = []; // if cols not in colgroup
	private $tableStr = '';

	/**
	 * Criando novo objeto Table
	 * @param type $id ID da tabela
	 * @param string $klass Classe da tabela
	 * @param array $attr_ar Atributos da tabela
	 */
	public function __construct($id = '', $klass = '', $attr_ar = []) {
	    // add rows to tbody unless addTSection called
	    $this->cur_section = &$this->tbody_ar[0];

	    $this->tableStr = "\n<table" . (!empty($id) ? " id=\"$id\"" : '' ) .
		    (!empty($klass) ? " class=\"$klass\"" : '' ) . $this->addAttribs($attr_ar) . ">\n";
	}

	/**
	 * 
	 * @param string $sec
	 * @param string $klass
	 * @param array $attr_ar
	 * @return \Table
	 */
	public function addTSection($sec, $klass = '', array $attr_ar = []) {
	    switch ($sec) {
		case 'thead':
		    $ref = &$this->thead;
		    break;
		case 'tfoot':
		    $ref = &$this->tfoot;
		    break;
		case 'tbody':
		    $ref = &$this->tbody_ar[count($this->tbody_ar)];
		    break;

		default: // tbody
		    $ref = &$this->tbody_ar[count($this->tbody_ar)];
	    }

	    $ref['name'] = $sec;
	    $ref['klass'] = $klass;
	    $ref['atts'] = $attr_ar;
	    $ref['rows'] = [];

	    $this->cur_section = &$ref;
	    return $this;
	}

	/**
	 * 
	 * @param string $span
	 * @param string $klass
	 * @param array $attr_ar
	 * @return \Table
	 */
	public function addColgroup($span = '', $klass = '', array $attr_ar = []) {
	    $group = array(
		'span' => $span,
		'klass' => $klass,
		'atts' => $attr_ar,
		'cols' => []
	    );

	    $this->colgroups_ar[] = &$group;
	    return $this;
	}

	/**
	 * 
	 * @param string $span
	 * @param string $klass
	 * @param array $attr_ar
	 * @return \Table
	 */
	public function addCol($span = '', $klass = '', array $attr_ar = []) {
	    $col = array(
		'span' => $span,
		'klass' => $klass,
		'atts' => $attr_ar
	    );

	    // in colgroup?
	    if (!empty($this->colgroups_ar)) {
		$group = &$this->colgroups_ar[count($this->colgroups_ar) - 1];
		$group['cols'][] = &$col;
	    } else {
		$this->cols_ar[] = &$col;
	    }
	    return $this;
	}

	/**
	 * 
	 * @param string $cap
	 * @param string $klass
	 * @param array $attr_ar
	 * @return \Table
	 */
	public function addCaption($cap, $klass = '', array $attr_ar = null) {
	    $this->tableStr.= "<caption" . (!empty($klass) ? " class=\"$klass\"" : '') .
		    $this->addAttribs($attr_ar) . '>' . $cap . "</caption>\n";
	    return $this;
	}

	/**
	 * 
	 * @param array $attr_ar
	 * @return string
	 */
	private function addAttribs(array $attr_ar = null) {
	    $str = '';
	    if ($attr_ar) {
		foreach ($attr_ar as $key => $val) {
		    $str .= ' ' . $key . '="' . htmlspecialchars($val) . '"';
		}
	    }
	    return $str;
	}

	/**
	 * 
	 * @param string $klass
	 * @param array $attr_ar
	 * @return \Table
	 */
	public function addRow($klass = '', array $attr_ar = []) {
	    // add row to current section
	    $this->cur_section['rows'][] = array(
		'klass' => $klass,
		'atts' => $attr_ar,
		'cells' => []
	    );
	    return $this;
	}

	/**
	 * 
	 * @param string $data
	 * @param string $klass
	 * @param string $type
	 * @param string $attr_ar
	 * @param boolean $include Se false não é incluido na tabela
	 * @return Table
	 * @throws Exception
	 */
	public function addCell($data = null, $klass = null, $type = null, array $attr_ar = null, $include = true) {
	    if ($include) {
		
		$cell = array(
		    'data' => $data,
		    'klass' => !is_array($klass) ? $klass : null,
		    'type' => $type == null ? ($this->cur_section['name'] == 'tbody' ? 'td' : 'th') : ($type == 'data' ? 'td' : $type),
		    'atts' => (is_array($klass) and is_null($attr_ar)) ? $klass : (array) $attr_ar,
		);

		if (empty($this->cur_section['rows'])) {
		    try {
			throw new Exception('You need to addRow before you can addCell');
		    } catch (Exception $ex) {
			$msg = $ex->getMessage();
			echo "<p>Error: $msg</p>";
		    }
		}

		// add to current section's current row's list of cells
		$count = count($this->cur_section['rows']);
		$curRow = &$this->cur_section['rows'][$count - 1];
		$curRow['cells'][] = &$cell;
	    }
	    return $this;
	}

	private function getRowCells($cells) {
	    $str = '';
	    foreach ($cells as $cell) {
		$tag = $cell['type'];
		$str .= (!empty($cell['klass']) ? "    <$tag class=\"{$cell['klass']}\"" : "    <$tag" ) .
			$this->addAttribs($cell['atts']) . ">" . $cell['data'] . "</$tag>\n";
	    }
	    return $str;
	}

	public function display() {
	    // get colgroups/cols
	    $this->tableStr .= $this->getColgroups();

	    // get sections and their rows/cells
	    $this->tableStr .=!empty($this->thead) ? $this->getSection($this->thead, 'thead') : '';
	    $this->tableStr .=!empty($this->tfoot) ? $this->getSection($this->tfoot, 'tfoot') : '';

	    foreach ($this->tbody_ar as $sec) {
		$this->tableStr .=!empty($sec) ? $this->getSection($sec, 'tbody') : '';
	    }

	    $this->tableStr .= "</table>\n";
	    return $this->tableStr;
	}

	// get colgroups/cols
	private function getColgroups() {
	    $str = '';

	    if (!empty($this->colgroups_ar)) {
		foreach ($this->colgroups_ar as $group) {

		    $str .= "<colgroup" . (!empty($group['span']) ? " span=\"{$group['span']}\"" : '' ) .
			    (!empty($group['klass']) ? " class=\"{$group['klass']}\"" : '' ) .
			    $this->addAttribs($group['atts']) . ">" .
			    $this->getCols($group['cols']) . "</colgroup>\n";
		}
	    } else {
		$str .= $this->getCols($this->cols_ar);
	    }

	    return $str;
	}

	private function getCols($ar) {
	    $str = '';
	    foreach ($ar as $col) {
		$str .= "<col" . (!empty($col['span']) ? " span=\"{$col['span']}\"" : '' ) .
			(!empty($col['klass']) ? " class=\"{$col['klass']}\"" : '') .
			$this->addAttribs($col['atts']) . ( $this->xhtml ? " />" : ">" );
	    }
	    return $str;
	}

	private function getSection($sec, $tag) {
	    $klass = !empty($sec['klass']) ? " class=\"{$sec['klass']}\"" : '';
	    $atts = !empty($sec['atts']) ? $this->addAttribs($sec['atts']) : '';

	    $str = "<$tag" . $klass . $atts . ">\n";

	    foreach ($sec['rows'] as $row) {
		$str .= (!empty($row['klass']) ? "  <tr class=\"{$row['klass']}\"" : "  <tr" ) .
			$this->addAttribs($row['atts']) . ">\n" .
			$this->getRowCells($row['cells']) . "  </tr>\n";
	    }

	    $str .= "</$tag>\n";

	    return $str;
	}

	public function __toString() {
	    return $this->display();
	}

    }
    