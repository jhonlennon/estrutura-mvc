<?php

    class indexController extends Controller {

	function indexAction() {
	    if (!IS_AJAX) {
		$this->pageAction();
	    }
	}
	
	function iconesAction() {

	    echo '<link rel="stylesheet" href="' . source('bootstrap/css/bootstrap.min.css') . '" />';
	    echo '<link rel="stylesheet" href="' . source('_cdn/fonts/font-awesome/css/font-awesome.min.css') . '" />';

	    preg_match_all('/\.(fa\-[a-z0-9\-]*?):before/', file_get_contents(ABSPATH . '/' . APP::getGlobal('public_path') . '/_cdn/fonts/font-awesome/css/font-awesome.min.css'), $icones);

	    echo '<div class="container" style="width: 100%; padding: 0 30px 30px 30px;" >';
	    echo '<div style="text-align: center;" class="row" >';
	    foreach ($icones[1] as $i => $icone) {
		echo '<div class="col-xs-2" style="margin-top: 30px;" >'
		. '<div style="border-radius: 4px; padding: 10px; background: #ededed; text-align: center;" >'
		. "<i class='fa {$icone} fa-3x' style='padding: 10px 0;' title='{$icone}' ></i>"
		. '<p style="margin: 0;" >' . $icone . '</p>'
		. '</div>'
		. '</div>';
		if ($i and ( $i + 1) % 6 === 0) {
		    echo '</div>'
		    . '<div class="row" >';
		}
	    }
	    echo '</div>'
	    . '</div>';
	}

	public function pageAction() {

	    // Buscando a página
	    if (!$Pagina = APP::getInstanceModel('Menu')->getPage(url_parans(0), Module::getUserLoged()->getType())) {
		if ($Pagina = APP::getInstance('MenuModel')->getPaginaPrincipal(Module::getUserLoged()->getType())) {
		    if ($Pagina->getArquivo() or $Pagina->GetController()) {
			header('Location: ' . url('page', [$Pagina->getId()]));
		    } else {
			exit('Página principal não possuí uma controller!');
		    }
		}
		exit;
	    }
	    APP::setGlobal('CurrentPage', $Pagina);

	    if (count($Pagina)) {

		// Redirecionando para a controller
		if ($Pagina->getController()) {

		    $parans = explode('/', $Pagina->getController());
		    $controller = APP::setController($parans[0]);

		    APP::setGlobal("Pagina", $Pagina);

		    if (!isset($parans[1]) or ! method_exists($controller, $Action = $parans[1] . 'Action')) {
			$Action = 'indexAction';
		    }

		    $controller->$Action($Pagina, $this);
		} else if ($Pagina->getArquivo()) {
		    // Carregando arquivo de visualização
		    try {
			$Content = view_load("{$Pagina->getArquivo()}", null, true);
		    }
		    // Página inválida
		    catch (Exception $ex) {
			$Content = ('Página inválida!');
		    }

		    $this->Layout($Content);
		}
	    } else {
		$this->notFoundPage();
	    }
	}
	
	function mapaAction(){
	    
	    Seo::reset();
	    
	    Seo::setTitle('Geolocalização');
	    
	    Seo::addCss(source('_cdn/bootstrap/css/bootstrap.min.css'));
	    Seo::addCss(source('_cdn/bootstrap/css/bootstrap-theme.min.css'));
	    
	    Seo::addJs(source('_cdn/js/jquery.min.js'));
	    Seo::addJs(source('_cdn/bootstrap/js/bootstrap.min.js'));
	    Seo::addJs('http://maps.google.com/maps/api/js?sensor=true');
	    Seo::addJs(source('_cdn/js/gmaps.js'));
	    	    
	    view_load('mapa');
	    
	}

	function notFoundPage() {
	    $this->Layout('');
	}

	function logOutAction() {
	    APP::getModule()->logOut();
	    header('Location: ' . url(''));
	}

	function Layout($Body = null, array $Variables = null, $Search = false) {
	    displayLayout($Body, $Variables, $Search);
	}

    }
    