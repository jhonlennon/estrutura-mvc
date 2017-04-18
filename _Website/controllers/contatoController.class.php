<?php

    class contatoController extends Controller {

	function indexAction() {
	    $this->displayPage(view_load('form/contato', [
		'assunto' => "Fale Conosco",
		'mensagem' => 'Envie uma mensagem utilizando o formulário que responderemos assim que possível.',
			    ], true), [
		'title' => 'Contato',
		'contato' => 'active',
	    ]);
	}

	function contato_sendAction() {
	    try {

		if (inputPost('nome1') or inputPost('nome2')) {
		    throw new Exception('Tentativa de SPAM bloqueada.');
		}

		if (!inputPost('nome')) {
		    throw new Exception('Informe seu nome.');
		} else if (!VALIDAR::email(inputPost('email'))) {
		    throw new Exception('E-mail inválido.');
		}

		$mail = new SMail;

		$mail->setFrom(inputPost('nome'), inputPost('email'));
		$mail->addAddress($mail->getConfig()->getNome(), $mail->getConfig()->getEmail());

		$p = inputPost();
		$mail->setContent(inputPost('assunto'), "<div><b>Nome:</b> {$p['nome']}</div>"
			. "<div><b>Telefone:</b> {$p['telefone']}</div>"
			. "<div><b>E-mail:</b> {$p['email']}</div>"
			. "<div><b>Estado:</b> {$p['estado']}</div>"
			. "<div><b>Cidade:</b> {$p['cidade']}</div>"
			. "<div><b>Mensagem:</b> {$p['mensagem']}</div>"
		);


		$mail->Send();

		json('Envio concluído com sucesso. Em breve entraremos em contato.', 1);
	    } catch (Exception $ex) {
		json($ex->getMessage());
	    }
	}

	function orcamentoAction() {
	    $this->displayPage(view_load('form/contato', [
		'mensagem' => 'Envie os dados da sua obra/reforma/manutenção para que possamos elaborar um orçamento, que responderemos assim que possível.',
		'assunto' => 'Orçamento',
			    ], true), [
		'title' => 'Orçamento',
		'orcamento' => 'active',
	    ]);
	}

	function localizacaoAction() {
	    $this->displayPage(view_load('form/localizacao', null, true), ['title' => 'Localização', 'localizacao' => 'active']);
	}

	function trabalheConoscoAction() {
	    $this->displayPage(view_load('form/trabalhe', null, true), ['title' => 'Trabalhe Conosco', 'trabalhe' => 'active']);
	}

	function trabalhe_sendAction() {
	    try {

		if (inputPost('nome1') or inputPost('nome2')) {
		    throw new Exception('Tentativa de SPAM bloqueada.');
		}

		if (!inputPost('nome')) {
		    throw new Exception('Informe seu nome.');
		} else if (!VALIDAR::email(inputPost('email'))) {
		    throw new Exception('E-mail inválido.');
		}

		$mail = new SMail;

		$mail->setFrom(inputPost('nome'), inputPost('email'));
		$mail->addAddress($mail->getConfig()->getNome(), $mail->getConfig()->getEmail());

		$p = inputPost();
		$mail->setContent('Trabalhe Conosco', "<h3>Dados Pessoais</h3>"
			. "<div><b>Nome:</b> {$p['nome']}</div>"
			. "<div><b>Telefone:</b> {$p['telefone']}</div>"
			. "<div><b>E-mail:</b> {$p['email']}</div>"
			. "<div><b>CEP:</b> {$p['cep']}</div>"
			. "<div><b>Endereço:</b> {$p['endereco']}, {$p['numero']}</div>"
			. "<div><b>Cidade/UF:</b> {$p['cidade']}/{$p['uf']}</div>"
			. "<br>"
			. "<h3>Escolaridade</h3>"
			. "<div><b>Curso:</b> {$p['es_curso']}</div>"
			. "<div><b>Instituicão:</b> {$p['es_instituicao']}</div>"
			. "<div><b>Grau:</b> {$p['es_grau']}</div>"
			. "<div><b>Situação:</b> {$p['es_situacao']}</div>"
			. "<div><b>Conhecimento em línguas estrangeiras?</b><br>" . nl2br($p['es_linguas']) . "</div>"
			. "<br>"
			. "<h3>Experiência Profissional</h3>"
			. "<div><b>Empresa:</b> {$p['ex_empresa']}</div>"
			. "<div><b>Cargo:</b> {$p['ex_cargo']}</div>"
			. "<div><b>Período:</b> {$p['ex_periodo']}</div>"
			. "<div><b>Funções desempenhadas?</b><br>" . nl2br($p['ex_funcoes']) . "</div>"
			. "<br>"
			. "<h3>Interesses</h3>"
			. "<div><b>Cargo:</b> {$p['in_cargo']}</div>"
			. "<div><b>Área de atuação:</b> {$p['in_area']}</div>"
			. "<div><b>Porque gostaria de trabalhar conosco?</b><br>" . nl2br($p['in_porque']) . "</div>"
		);

		$mail->Send();

		json('Dados enviados com sucesso. Em breve entraremos em contato.', 1);
	    } catch (Exception $ex) {
		json($ex->getMessage());
	    }
	}

	function displayPage($body, array $vars = null) {
	    displayLayout(view_load('contato', extend(['body' => $body], $vars), true));
	}

    }
    