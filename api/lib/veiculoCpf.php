<?php

function filtroCpf($res) {
	if(!stristr($res, 'Renavam</td>')) {
		return array('msg' => 'nada encontrado');
	}

	$dados = explode('<table width="100%" class="tabelaHs">', $res);

	$veiculo = filtroVeiculoCpf($dados);
	$proprietario = filtroProprietarioCpf($dados);
	$retorno = array(
		'veiculo' => $veiculo,
		'proprietario' => $proprietario
	);
	return $retorno;
}

function filtroProprietarioCpf($dados) {
	$dom = new DOMDocument();

	$dados11 = $dados[1];
	$dados11 = corta($dados11, '<tbody>', '</tbody>');
	$dados12 = $dados[2];
	$dados12 = corta($dados12, '<tbody>', '</tbody>');
	$dados13 = $dados[3];
	$dados13 = corta($dados13, '<tbody>', '</tbody>');

	$dados18 = $dados[8];
	$dados18 = corta($dados18, '<tbody>', '</tbody>');

	$dados19 = $dados[9];
	$dados19 = corta($dados19, '<tbody>', '</tbody>');


	$dom->loadHTML($dados11);
	$Detail = $dom->getElementsByTagName('td');
	$nome   = clearStr($Detail->item(0)->nodeValue);
	$mae    = clearStr($Detail->item(1)->nodeValue);
	$recVec = clearStr($Detail->item(2)->nodeValue);

	$dom->loadHTML($dados12);
	$Detail2 = $dom->getElementsByTagName('td');
	$idade   = clearStr($Detail2->item(1)->nodeValue);
	$signo   = clearStr($Detail2->item(2)->nodeValue);
	$sexo    = clearStr($Detail2->item(3)->nodeValue);
	$situacaodono = clearStr($Detail2->item(4)->nodeValue);
	$nascimento   = clearStr($Detail2->item(0)->nodeValue);

	$dom->loadHTML($dados19);

	$Detail9   = $dom->getElementsByTagName('td');
	$endEstado = clearStr($Detail9->item(0)->nodeValue);
	$endCidade = clearStr($Detail9->item(1)->nodeValue);
	$endBairro = clearStr($Detail9->item(2)->nodeValue);
	$endRua    = clearStr($Detail9->item(3)->nodeValue);
	$endNumero = clearStr($Detail9->item(4)->nodeValue);
	$endCep    = clearStr($Detail9->item(6)->nodeValue);
	$endComplemento = clearStr($Detail9->item(5)->nodeValue);

	$dom->loadHTML($dados13);
	$Detail3   = $dom->getElementsByTagName('td');
	$cpfcnpj   = clearStr($Detail3->item(4)->nodeValue);


	return array(
			'doc'  => $cpfcnpj,
			'nome' => $nome,
			'mae'  => $mae,
			'nascimento' => $nascimento,
			'idade'  => $idade,
			'signo'  => $signo,
			'sexo'   => $sexo,
			'situacao' => $situacaodono,
			'endereco' => array(
				'bairro' => $endBairro,
				'rua'    =>  $endRua,
				'numero' => $endNumero,
				'complemento' => $endComplemento,
				'cep'    => $endCep,
				'estado' => $endEstado,
				'cidade' => $endCidade
			)
	);
}

function filtroVeiculoCpf($dados) {
	$dom = new DOMDocument();

	$dados13 = $dados[3];
	$dados13 = corta($dados13, '<tbody>', '</tbody>');
	$dados14 = $dados[4];
	$dados14 = corta($dados14, '<tbody>', '</tbody>');
	$dados15 = $dados[5];
	$dados15 = corta($dados15, '<tbody>', '</tbody>');
	$dados17 = $dados[7];
	$dados17 = corta($dados17, '<tbody>', '</tbody>');

	$dom->loadHTML($dados13);
	$Detail3   = $dom->getElementsByTagName('td');
	$categoria = clearStr($Detail3->item(0)->nodeValue);
	$placa     = clearStr($Detail3->item(1)->nodeValue);
	$chassi    = clearStr($Detail3->item(2)->nodeValue);
	$renavam   = clearStr($Detail3->item(3)->nodeValue);

	$dom->loadHTML($dados14);
	$Detail4 = $dom->getElementsByTagName('td');
	$cor     = clearStr($Detail4->item(1)->nodeValue);
	$anofab  = clearStr($Detail4->item(2)->nodeValue);
	$anomol  = clearStr($Detail4->item(3)->nodeValue);
	$anoexercicio = clearStr($Detail4->item(4)->nodeValue);
	$marcmodelo   = clearStr($Detail4->item(0)->nodeValue);

	$dom->loadHTML($dados15);
	$Detail5 = $dom->getElementsByTagName('td');
	$municipioEmplacamento  = clearStr($Detail5->item(0)->nodeValue);
	$EstadoEmplacamento     = clearStr($Detail5->item(1)->nodeValue);

	$dom->loadHTML($dados17);
	$Detail7 = $dom->getElementsByTagName('td');
	$registroRoubo    		 = clearStr($Detail7->item(1)->nodeValue);
	$sinalizacaoRoubo    	 = clearStr($Detail7->item(2)->nodeValue);
	$sinalizacaoLocalizacao  = clearStr($Detail7->item(3)->nodeValue);
	$ordemJudidicalApreensao = clearStr($Detail7->item(0)->nodeValue);

	return array(
			'placa'   => $placa,
			'chassi'  => $chassi,
			'renavam' => $renavam,
			'cor'     => $cor,
			'ano_fab' => $anofab,
			'ano_modelo'    => $anomol,
			'marca_modelo'  => $marcmodelo,
			'categoria' => $categoria,
			'municipio_emplacamento'   => $municipioEmplacamento,
			'estado_emplacamento'      => $EstadoEmplacamento,
			'ordem_judicial_apreensao' => $ordemJudidicalApreensao,
			'registro_de_roubo'    => $registroRoubo,
			'sinalizacao_de_roubo' => $sinalizacaoRoubo,
			'sinalizacao_de_localizacao' => $sinalizacaoLocalizacao,
	);
}
