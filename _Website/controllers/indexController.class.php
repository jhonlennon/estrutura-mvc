<?php

    class indexController extends Controller {

	function indexAction() {
	    displayLayout(view_load('home', null, true), ['class' => 'home']);
	}

	function comofuncionaAction() {
	    displayLayout(view_load('pagina', [
		'v' => (new PaginasModel)->getByRef('comofunciona', true),
			    ], true));
	}

	function institucionalAction() {
	    displayLayout(view_load('pagina', [
		'v' => (new PaginasModel)->getByRef('institucional', true),
			    ], true));
	}

	function servicosAction() {
	    displayLayout(view_load('servicos', [
		'servicos' => (new ServicosModel)->Lista('WHERE a.status = 1 ORDER BY a.title ASC'),
	    ], true));
	}

    }
    