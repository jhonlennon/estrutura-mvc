<?php

    class CorreiosHellper {

	private $Produtos = [];

	/**
	 * Adiciona um produto
	 * @param int $altura cm
	 * @param int $largura cm
	 * @param int $comprimento cm
	 * @param int $peso grama
	 */
	function addProduto($altura, $largura, $comprimento, $peso, $quantidade) {
	    $this->Produtos[] = [
		'altura' => $altura,
		'largura' => $largura,
		'comprimento' => $comprimento,
		'cubagem' => (int) ((($altura * $largura * $comprimento) / 6000) * $quantidade),
		'peso' => $peso * $quantidade,
	    ];
	}

	function getPeso() {
	    $pesos = [
		'peso' => 0,
		'cubagem' => 0,
	    ];
	    foreach ($this->Produtos as $v) {
		$pesos['cubagem'] += $v['cubagem'];
		$pesos['peso'] += $v['peso'];
	    }
	    return (int) (($pesos['cubagem'] < 10 ? $pesos['peso'] : max($pesos['cubagem'], $pesos['peso'])) / 1000);
	}

	function calcFrete($cepRemetente, $cepDestinatario) {

	    if (empty($this->Produtos)) {
		throw new CorreiosHellperException('Nenhum produto foi adicionado para o cálculo.');
	    }

	    $calc = max(1, $this->getPeso() / 30);

	    $dados = [
		'StrRetorno' => 'xml',
		'nCdEmpresa' => '',
		'sDsSenha' => '',
		'nVlDiametro' => 0,
		'nCdServico' => '41106,40010',
		'sCepOrigem' => preg_replace('/[^0-9]/', NULL, $cepRemetente),
		'sCepDestino' => preg_replace('/[^0-9]/', NULL, $cepDestinatario),
		'nVlComprimento' => 16,
		'nVlLargura' => 11,
		'nVlAltura' => 2,
		'nVlPeso' => max(1, min(30, $this->getPeso())),
		'nCdFormato' => 1,
		'sCdMaoPropria' => 'N',
		'nVlValorDeclarado' => 0,
		'sCdAvisoRecebimento' => 'N',
	    ];

	    $url = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?' . http_build_query($dados);
	    
	    
	    if ($xml = @simplexml_load_file($url)) {
		
		$frete = [
		    'pac' => [
			'codigo' => 41106,
			'cepR' => Mask::cep($cepRemetente),
			'cepD' => Mask::cep($cepDestinatario),
			'valor' => Number::float(Number::float($xml->cServico[0]->Valor, 2) * $calc, 2),
			'prazo' => (int) $xml->cServico[0]->PrazoEntrega,
			'errorCode' => (string) $xml->cServico[0]->Erro,
			'error' => $this->errorMessage($xml->cServico[0]->Erro),
			'errorMessage' => str_replace(' Busca CEP', ' <a href="http://www.buscacep.correios.com.br/" target="_blank" ><b>Busca CEP</b></a>', (string) $xml->cServico[1]->MsgErro),
		    ],
		    'sedex' => [
			'codigo' => 40010,
			'cepR' => Mask::cep($cepRemetente),
			'cepD' => Mask::cep($cepDestinatario),
			'valor' => Number::float(Number::float($xml->cServico[1]->Valor, 2) * $calc, 2),
			'prazo' => (int) $xml->cServico[1]->PrazoEntrega,
			'errorCode' => (string) $xml->cServico[1]->Erro,
			'error' => $this->errorMessage($xml->cServico[1]->Erro),
			'errorMessage' => str_replace(' Busca CEP', ' <a href="http://www.buscacep.correios.com.br/" target="_blank" ><b>Busca CEP</b></a>', (string) $xml->cServico[1]->MsgErro),
		    ],
		];
		
		if ($frete['pac']['error']) {
		    throw new CorreiosHellperException($frete['pac']['errorMessage'], (int) $frete['pac']['errorCode']);
		}

		return $frete;
	    } else {
		throw new CorreiosHellperException('Não foi possível calcular o frete.', (int) '-999');
	    }
	}

	function errorMessage($erro) {
	    switch ((string) $erro) {
		case '0': return false;
		    break;
		case '-1': return 'Código de serviço inválido';
		    break;
		case '-2': return 'CEP de origem inválido';
		    break;
		case '-3': return 'CEP de destino inválido';
		    break;
		case '-4': return 'Peso excedido';
		    break;
		case '-5': return 'O Valor Declarado não deve exceder R$ 10.000,00';
		    break;
		case '-6': return 'Serviço indisponível para o trecho informado';
		    break;
		case '-7': return 'O Valor Declarado é obrigatório para este serviço';
		    break;
		case '-8': return 'Este serviço não aceita Mão Própria';
		    break;
		case '-9': return 'Este serviço não aceita Aviso de Recebimento';
		    break;
		case '-10': return 'Precificação indisponível para o trecho informado';
		    break;
		case '-11': return 'Para definição do preço deverão ser informados, também, o comprimento, a largura e altura do objeto em centímetros (cm).';
		    break;
		case '-12': return 'Comprimento inválido.';
		    break;
		case '-13': return 'Largura inválida.';
		    break;
		case '-14': return 'Altura inválida.';
		    break;
		case '-15': return 'O comprimento não pode ser maior que 105 cm.';
		    break;
		case '-16': return 'A largura não pode ser maior que 105 cm.';
		    break;
		case '-17': return 'A altura não pode ser maior que 105 cm.';
		    break;
		case '-18': return 'A altura não pode ser inferior a 2 cm.';
		    break;
		case '-20': return 'A largura não pode ser inferior a 11 cm.';
		    break;
		case '-22': return 'O comprimento não pode ser inferior a 16 cm.';
		    break;
		case '-23': return 'A soma resultante do comprimento + largura + altura não deve superar a 200 cm.';
		    break;
		case '-24': return 'Comprimento inválido.';
		    break;
		case '-25': return 'Diâmetro inválido';
		    break;
		case '-26': return 'Informe o comprimento.';
		    break;
		case '-27': return 'Informe o diâmetro.';
		    break;
		case '-28': return 'O comprimento não pode ser maior que 105 cm.';
		    break;
		case '-29': return 'O diâmetro não pode ser maior que 91 cm.';
		    break;
		case '-30': return 'O comprimento não pode ser inferior a 18 cm.';
		    break;
		case '-31': return 'O diâmetro não pode ser inferior a 5 cm.';
		    break;
		case '-32': return 'A soma resultante do comprimento + o dobro do diâmetro não deve superar a 200 cm.';
		    break;
		case '-33': return 'Sistema temporariamente fora do ar. Favor tentar mais tarde.';
		    break;
		case '-34': return 'Código Administrativo ou Senha inválidos.';
		    break;
		case '-35': return 'Senha incorreta.';
		    break;
		case '-36': return 'Cliente não possui contrato vigente com os Correios.';
		    break;
		case '-37': return 'Cliente não possui serviço ativo em seu contrato.';
		    break;
		case '-38': return 'Serviço indisponível para este código administrativo.';
		    break;
		case '-39': return 'Peso excedido para o formato envelope';
		    break;
		case '-40': return 'Para definicao do preco deverao ser informados, tambem, o comprimento e a largura e altura do objeto em centimetros (cm).';
		    break;
		case '-41': return 'O comprimento nao pode ser maior que 60 cm.';
		    break;
		case '-42': return 'O comprimento nao pode ser inferior a 16 cm.';
		    break;
		case '-43': return 'A soma resultante do comprimento + largura nao deve superar a 120 cm.';
		    break;
		case '-44': return 'A largura nao pode ser inferior a 11 cm.';
		    break;
		case '-45': return 'A largura nao pode ser maior que 60 cm.';
		    break;
		case '-888': return 'Erro ao calcular a tarifa';
		    break;
		case '006': return 'Localidade de origem não abrange o serviço informado';
		    break;
		case '007': return 'Localidade de destino não abrange o serviço informado';
		    break;
		case '008': return 'Serviço indisponível para o trecho informado';
		    break;
		case '009': return 'CEP inicial pertencente a Área de Risco.';
		    break;
		case '010': return 'Área com entrega temporariamente sujeita a prazo diferenciado.';
		    break;
		case '011': return 'CEP inicial e final pertencentes a Área de Risco';
		    break;
		case '7': return 'Serviço indisponível, tente mais tarde';
		    break;
		case '99': return 'Outros erros diversos do .NET';
		    break;
		default:
		    return "CEP de destino não encontrado na base de dados dos Correios.";
	    }
	}

    }

    class CorreiosHellperException extends Exception {
	
    }
    