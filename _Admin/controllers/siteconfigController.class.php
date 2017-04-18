<?php

    class siteconfigController extends Controller {

	function indexAction() {

	    echo form_open([
		'data-values' => $this->getModel()->get()->toArray(true),
		'data-adminpage' => [
		    'actionUpdate' => 'insert',
		    'alertSuccess' => true,
		    'autoReset' => false,
		    'autoSearch' => false,
		]
	    ]);
	    
	    echo form_hidden([
		'latitude' => null,
		'longitude' => null,
		'zoom' => null,
	    ]);

	    echo (new PanelBootstrap)
		    ->setBody((new Grid12('md'))
			    ->addColumn(formLabel('Título', formInput('title')))
			    ->addColumn(formLabel('Telefone', formInput('telefone', null, 'text', ['class' => 'mask-telefone'])), 3)
			    ->addColumn(formLabel('Celular', formInput('celular', null, 'text', ['class' => 'mask-telefone'])), 3)
			    ->addColumn(formLabel('E-mail', formInput('email')), 6)
			    ->addColumn(formLabel('Descrição', formInput('descricao')), 6)
			    ->addColumn(formLabel('Keywords', formInput('keywords')), 6)
			    ->addColumn(formLabel('Facebook', formInput('facebook')), 6)
			    ->addColumn(formLabel('Facebook (Like)', formInput('facebooklike')), 6)
			    ->addColumn(formLabel('CEP', formInput('cep', null, 'text', ['class' => 'mask-cep'])), 3)
			    ->addColumn(formLabel('Logradouro', formInput('logradouro')), 6)
			    ->addColumn(formLabel('Número', formInput('numero')), 3)
			    ->addColumn(formLabel('Bairro', formInput('bairro')), 3)
			    ->addColumn(formLabel('Estado', formSelect('estado', newModel('Estados')->options('uf'))), 3)
			    ->addColumn(formLabel('Cidade', formSelect('cidade')), 6)
		    )
		    ->setFooter((new Grid12('md'))
			    ->addColumn(formButton('<i class="fa fa-map-marker" ></i> Mapa', 'button', 'btn-marker btn-success'), 6)
			    ->addColumn(formButton('<i class="fa fa-save" ></i> Salvar'), 'col-md-6 text-right'))

	    ;

	    echo form_close();

	    displayLayout(ob_get_clean());
	}
	
	function insertAction() {
	    try {
		$v = $this->getModel()->get();
		$v->__setValues(inputPost());
		$v->Save();
		json('Dados atualizados com sucesso.', 1);
	    } catch (Exception $ex) {
		json($ex->getMessage(), 0);
	    }
	}

	/** @return SiteConfigModel */
	function getModel() {
	    return newModel('SiteConfig');
	}

    }
    