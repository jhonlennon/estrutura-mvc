<?php

    class galeriasController extends Controller {

	function indexAction(MenuVO $Pagina) {
	    if (!IS_AJAX) {

		$values = stringToArray($Pagina->getVariaveis());

		$categorias = formOption('-- Selecione --', '');
		$mCategorias = newModel('Categorias');

		foreach ($mCategorias->Lista('WHERE a.ref = :ref AND a.root = 0 AND a.status = 1 ORDER BY a.ordem ASC', ['ref' => $values['ref']]) as $v) {
		    $categorias .= formOption($v->getTitle(), $v->getId());
		}

		APP::getInstanceController('index')->Layout(form([
		    'id' => 'form-this',
		    'data-adminpage' => [
			'controller' => 'galerias',
			'saveValues' => ['ref' => $values['ref']]
		    ]
				], (new PanelBootstrap)
					->setBody(formInput('id', null, 'hidden')
						. (new Grid12('md'))
						->addColumn(formLabel('Categoria', formSelect('categoria', $categorias, ['required'])), 12)
						->addColumn(formLabel('Título', formInput('title')), 12)
						//->addColumn(formLabel('Texto', formTextarea('texto', null, ['data-ckeditor' => true])))
						->addColumn(formLabel('Descrição', formInput('descricao')), 6)
						->addColumn(formLabel('Keywords', formInput('keywords')), 6)
					)
					->setFooter((new Grid12)
						//->addColumn(formButtonFile('<i class="fa fa-image" ></i> Imagens', 'imagens[]', 'image/*'), 12, null, ['class' => 'text-left'])
						->addColumn('<div class="text-right" >'
							. '<div class="btn-group" >'
							. formButton('<i class="fa fa-trash" ></i> Limpar', 'reset')
							. formButton('<i class="fa fa-save" ></i> Salvar')
							. '</div>'
							. '</div>', 12)
					)
			)
		);
	    }
	}

	function listAction() {
	    $busca = $this->getModel()->Busca(inputPost(), inputPost('page'));
	    if ($busca->getCount()) {
		echo (new PanelBootstrap)
			->setHeader('<h3 class="panel-title" >Lista</h3>')
			->setBody(call_user_func(function($Registros) {
				    $t = (new Table(null, 'table table-bordered table-striped'))
					    ->addTSection('thead')
					    ->addRow()
					    ->addCell('Img', ['width' => 80])
					    ->addCell('Categoria')
					    ->addCell('Título')
					    ->addCell('Ações', ['width' => 120])
					    ->addTSection('tbody');
				    /* @var $v GaleriaVO */
				    $v = new GaleriaVO;
				    foreach ($Registros as $v) {
					$t->addRow()
					->addCell('<img src="' . $v->imgCapa(true)->Redimensiona(30, 30) . '" />', 'text-center')
					->addCell(newModel('Categorias')->getByLabel('id', $v->getCategoria())->getTitle())
					->addCell($v->getTitle())
					->addCell('<div class="btn-group btn-group-xs" >'
						. '<div class="btn btn-default" data-status="' . $v->getId() . '" ><i class="fa fa-eye' . ($v->getStatus() == 1 ? null : '-slash') . '" ></i></div>'
						. '<div class="btn btn-default" onClick="galeria(\'' . $v->gettable() . '\', \'' . $v->getid() . '\', \'' . htmlspecialchars($v->getTitle()) . '\')" ><i class="fa fa-image" ></i></div>'
						. '<div class="btn btn-default" data-editar="' . $v . '" ><i class="fa fa-edit" ></i></div>'
						. '<div class="btn btn-danger" data-excluir="' . $v->getId() . '" ><i class="fa fa-remove" ></i></div>'
						. '</div>', 'text-center');
				    }
				    return $t->display();
				}, $busca->getRegistros()))
			->setFooter($busca->display());
	    } else {
		echo '<div class="alert alert-warning" >Nenhuma galeria cadastrada até o momento.</div>';
	    }
	}

	function excluirAction() {
	    try {
		$this->getModel()->Excluir($this->getModel()->getByLabel('id', inputPost('id')));
		exitJson('Excluído com sucesso.', 1);
	    } catch (Exception $ex) {
		exitJson($ex->getMessage());
	    }
	}

	function statusAction() {
	    if ($v = $this->getModel()->getByLabel('id', inputPost('id'))) {
		$v->setStatus($v->getStatus() == 1 ? 0 : 1);
		$this->getModel()->Save($v);
	    }
	}

	function insertAction() {
	    $this->Save();
	}

	function updateAction() {
	    $this->Save((int) inputPost('id'));
	}

	function Save($id = null) {
	    try {
		if ($id !== null) {
		    if (!$v = $this->getModel()->getByLabel('id', $id)) {
			throw new Exception('Registro inválido.');
		    }
		} else {
		    $v = $this->getModel()->newValueObject();
		}

		$v
			->input('categoria')
			->input('ref')
			->input('data')
			->input('texto')
			->input('descricao')
			->input('keywords')
			->input('title');

		$this->getModel()->Save($v);

		$v->imgAdd('imagens');

		exitJson('Salva com sucesso.', 1);
	    } catch (Exception $ex) {
		exitJson($ex->getMessage());
	    }
	}

	/** @return GaleriasModel */
	function getModel() {
	    return APP::getInstanceModel('Galerias');
	}

    }
    