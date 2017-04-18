<?php

    class smtpconfigController extends Controller {

	function indexAction(MenuVO $Pagina = null) {
	    if ($Pagina) {
		APP::getInstanceController('index')->Layout(form([
		    'data-values' => $this->getModel()->getConfig()->toArray(true),
		    'data-adminpage' => [
			'controller' => str_replace('Controller', null, get_class($this)),
			'autoSearch' => false,
			'autoReset' => false,
		    ]
				], (new PanelBootstrap)
					->setBody(formInput('id', null, 'hidden')
						. (new Grid12('md'))
						->addColumn(formLabel('Nome', formInput('nome', null, 'text', ['required' => true])), 4)
						->addColumn(formLabel('E-mail', formInput('email', null, 'email', ['required' => true])), 8)
						->addColumn(formLabel('Assunto', formInput('assunto')), 12)
						->addColumn(formLabel('Host', formInput('host', null, 'text', ['required' => true])), 10)
						->addColumn(formLabel('Porta', formInput('porta', null, 'text', ['required' => true])), 2)
						->addColumn(formLabel('Username', formInput('login', null, 'text', ['required' => true])), 4)
						->addColumn(formLabel('Password', formInput('senha', null, 'password', ['placeholder' => 'Mater senha atual'])), 4)
						->addColumn(formLabel('Autenticar', formSelect('autenticar', [
						    1 => 'Sim',
						    0 => 'Não',
							])), 2)
						->addColumn(formLabel('Protocolo', formSelect('protocolo', [
						    '' => 'Nenhum',
						    'ssl' => 'SSL',
						    'tls' => 'TLS',
							])), 2)
					)
					->setFooter((new Grid12)
						->addColumn('<div class="text-right" >'
							. '<div class="btn-group" >'
							. formButton('<i class="fa fa-save" ></i> Salvar', 'submit')
							. '</div>'
							. '</div>'
						)
					)
		));
	    }
	}

	function updateAction() {
	    try {
		if (!$v = $this->getModel()->getByLabel('id', inputPost('id'))) {
		    throw new Exception('Configurações inválidas.');
		}

		$v
			->input('nome')
			->input('email')
			->input('assunto')
			->input('host')
			->input('porta')
			->input('login')
			->input('autenticar')
			->input('protocolo');

		if (inputPost('senha')) {
		    $v->input('senha');
		}

		$this->getModel()->Save($v);

		exitJson('Configurações salvas com sucesso.', 1);
	    } catch (Exception $ex) {
		exitJson($ex->getMessage(), 0);
	    }
	}

	/** @return SmtpModel */
	function getModel() {
	    return APP::getInstance('SmtpModel');
	}

    }
    