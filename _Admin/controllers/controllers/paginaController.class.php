<?php

    class paginaController extends Controller {

	function indexAction(MenuVO $p) {

	    $vars = stringToArray($p->getVariaveis());

	    $pagina = $this->getModel()->getByRef($vars['ref'], true);

	    echo form_open([
		'data-values' => $pagina->toArray(true),
		'data-adminpage' => [
		    'saveValues' => ['ref' => $pagina->getRef()],
		    'autoSearch' => false,
		    'autoReset' => false,
		    'alertSuccess' => true,
		]
	    ]);

	    echo form_hidden(['id' => null]);
	    
	    echo (new PanelBootstrap)
		    ->setBody((new Grid12('md'))
			    ->addColumn(formLabel('Title', formInput('title')), 12)
			    ->addColumn(formLabel('Texto', formTextarea('texto', null, ['data-ckeditor', 'height' => 300])), 12)
			    ->addColumn(formLabel('Descrição', formInput('descricao')), 6)
			    ->addColumn(formLabel('Keywords', formInput('keywords')), 6)
		    )
		    ->setFooter(formButton('<i class="fa fa-save" ></i> Salvar'), ['class' => 'text-right'])
	    ;

	    echo form_close();


	    displayLayout(ob_get_clean());
	}

	function save(PaginaVO $p) {
	    try {

		$p
			->input('ref')
			->input('title')
			->input('descricao')
			->input('keywords')
			->input('texto')
			->Save()
		;

		json('Salvo com sucesso.', 1);
	    } catch (Exception $ex) {
		json($ex->getMessage(), 0);
	    }
	}

	/** @return PaginasModel */
	function getModel() {
	    return newModel('Paginas');
	}

    }
    