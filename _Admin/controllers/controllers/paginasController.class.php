<?php

    class paginasController extends Controller {

	function indexAction(MenuVO $P, indexController $cIndex) {

	    $v = stringToArray($P->getVariaveis());

	    $cIndex->layout(form([
		'id' => 'form-this',
		'data-adminpage' => [
		    'saveValues' => ['ref' => $v['ref']],
		    'searchValues' => ['ref' => $v['ref'], 'galeria' => !empty($v['galeria']) ? 1 : 0],
		]
			    ], formInput('id', 0, 'hidden')
			    . (new PanelBootstrap)
				    ->setBody((new Grid12('md'))
					    ->addColumn(formLabel('Título', formInput('title', null, 'text', ['maxlength' => 250])))
					    ->addColumn(formLabel('Texto', formTextarea('texto', null, ['data-ckeditor' => ''])))
					    ->addColumn(formLabel('Descrição', formInput('descricao', null, 'text', ['maxlength' => 250])), 6)
					    ->addColumn(formLabel('Keywords', formInput('keywords', null, 'text', ['maxlength' => 250])), 6)
				    )
				    ->setFooter((new Grid12)
					    ->addColumn(
						    !empty($v['galeria']) ? formButtonFile('<i class="fa fa-camera" ></i> Imagens', 'file_imagens', 'image/jpeg') : null
						    , 4)
					    ->addColumn('<div class="btn-group" >'
						    . formButton('<i class="fa fa-trash" ></i> Limpar', 'reset')
						    . formButton('<i class="fa fa-save" ></i> Salvar')
						    . '</div>', 8, null, ['class' => 'text-right']))
		    )
	    );
	}

	function Save(PaginaVO $v) {
	    try {

		$v
			->input('ref')
			->input('title')
			->input('texto')
			->input('descricao')
			->input('keywords')
			->Save()
			->imgAdd('file_imagens')
		;

		exitJson('Salvo com sucesso', 1);
	    } catch (Exception $ex) {
		exitJson($ex->getMessage(), 0);
	    }
	}

	function excluirAction() {
	    try {
		$this->voById((int) inputPost('id'))->Excluir();
		json('Excluído', 1);
	    } catch (Exception $ex) {
		json($ex->getMessage(), 0);
	    }
	}

	function listAction() {
	    $Busca = $this->getModel()->Busca(inputPost(), inputPost('page'), 10);
	    if ($Busca->getCount()) {

		$t = new Table(null, 'table table-bordered table-striped table-hover');
		$t
			->addtsection('thead')
			->addrow()
			->addcell('Título')
			->addcell('Ações', ['width' => 120])
			->addtsection('tbody')
		;
		foreach ($Busca->getRegistros() as $v) {
		    $t->addrow()
			    ->addcell($v->gettitle())
			    ->addcell('<div class="btn-group btn-group-xs" >'
				    . '<div class="btn btn-default" data-editar="' . $v . '" ><i class="fa fa-edit" ></i></div>'
				    . (inputPost('galeria') ? '<div class="btn btn-default" onClick="galeria(\'' . $v->getTable() . '\', ' . $v->getId() . ')" ><i class="fa fa-image" ></i></div>' : null)
				    . '<div class="btn btn-danger" data-excluir="' . $v->getid() . '" ><i class="fa fa-remove" ></i></div>'
				    . '</div>', 'text-center')
		    ;
		}

		echo (new PanelBootstrap())
			->setBody($t)
			->setFooter($Busca->display())
		;
	    }
	}

	/** @return PaginasModel */
	function getModel() {
	    return APP::getInstanceModel('Paginas');
	}

    }
    