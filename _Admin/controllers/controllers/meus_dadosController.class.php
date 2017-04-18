<?php

    class meus_dadosController extends Controller {

	/** @var UserVO */
	private $User;

	public function __construct() {
	    $this->User = Module::getUserLoged();
	}

	function indexAction() {

	    Seo::addJs('<script>'
		    . '$("#form-this").on("success", function(event, e){'
		    . 'if(e.result == 1){'
		    . '$("img.img-user").attr("src", e.foto);'
		    . '}'
		    . '});'
		    . '</script>');

	    echo form_open([
		'id' => 'form-this',
		'data-values' => $this->User->toArray(true),
		'data-adminpage' => [
		    'autoSearch' => false,
		    'autoReset' => false,
		    'alertSuccess' => true,
		]
	    ]);

	    echo (new PanelBootstrap)
		    # Body
		    ->setBody((new Grid12('md'))
			    ->addColumn(formLabel('Nome', formInput('nome', null, 'text')), 12)
			    ->addColumn(formLabel('E-mail', formInput('email', null, 'email', ['required' => true])), 6)
			    ->addColumn(formLabel('Telefone', formInput('telefone', null, 'text', ['class' => 'mask-telefone'])), 3)
			    ->addColumn(formLabel('Celular', formInput('celular', null, 'text', ['class' => 'mask-telefone'])), 3)
			    ->addColumn(formLabel('Login', formInput('login', null, 'text', ['pattern' => '[A-Za-z0-9]{5,20}', 'required' => true])), 9)
			    ->addColumn(formLabel('Senha', formInput('senha', null, 'text', ['placeholder' => 'Manter senha atual', 'pattern' => '[A-Za-z0-9]{5,20}', 'maxlength' => '20'])), 3))

		    # Footer
		    ->setFooter(
			    (new Grid12)
			    ->addColumn(formButtonFile('<i class="fa fa-camera" ></i> Foto', 'file_foto', 'image/jpeg'), 4)
			    ->addColumn('<div class="text-right" >'
				    . '<div class="btn-group" >'
				    . formButton('<i class="fa fa-save" ></i> Salvar')
				    . '</div>'
				    . '</div>', 8)
		    )
	    ;

	    echo form_close();

	    displayLayout(ob_get_clean(), [
		'Title' => 'Meus dados',
		'Subtitle' => 'Editar informações da minha conta.'
	    ]);
	}

	function insertAction() {
	    try {

		$this->User
			->input('nome')
			->input('telefone')
			->input('celular')
			->input('login')
			->input('email')
		;

		if (inputPost('senha')) {
		    $this->User->setSenha(inputPost('senha'));
		}

		$this->User->Save();
		$this->User->imgAddCapa('file_foto');

		exitJson([
		    'message' => 'Dados atualizados com sucesso.',
		    'foto' => $this->User->imgCapa()->Redimensiona(160, 160),
			], 1);
	    } catch (Exception $ex) {
		exitJson($ex->getMessage(), 1);
	    }
	}

    }
    