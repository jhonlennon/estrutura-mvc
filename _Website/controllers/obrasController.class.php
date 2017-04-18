<?php

    class obrasController extends Controller {

	function verAction() {
	    if ($v = (new PaginasModel)->getByLabel('id', url_parans(0)) and $v->getStatus() == 1) {
		displayLayout(view_load('obras', [
		    'v' => $v,
		    'imagens' => $v->imgList(),
		    'obras' => newModel('Paginas')->Busca(['!id' => $v->getId(), 'ref' => 'obras', 'status' => 1], url_parans('pagina'), 10),
		    'link' => url('obras/ver', [$v->getId(), $v->getTitle()]) . '/pagina/#page#',
				], true));
	    } else {
		location(url());
	    }
	}

    }
    