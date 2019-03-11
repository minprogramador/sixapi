<?php

function filtroCnpj($res) {
	if(!stristr($res, 'Renavam</td>')) {
		return array('msg' => 'nada encontrado');
	}

	$dados = explode('<table width="100%" class="tabelaHs">', $res);

	$veiculo 	  = filtroVeiculoCnpj($dados);
	$proprietario = filtroProprietarioCnpj($dados);

	$retorno = array(
		'veiculo' => $veiculo,
		'proprietario' => $proprietario
	);

	return $retorno;
}

function filtroVeiculoCnpj($dados) {
	$dom     = new DOMDocument();

	$dados11 = $dados[1];
	$dados11 = corta($dados11, '<tbody>', '</tbody>');
	$dados14 = $dados[4];
	$dados14 = corta($dados14, '<tbody>', '</tbody>');
	$dados15 = $dados[5];
	$dados15 = corta($dados15, '<tbody>', '</tbody>');
	$dados16 = $dados[6];
	$dados16 = corta($dados16, '<tbody>', '</tbody>');
	$dados18 = $dados[8];
	$dados18 = corta($dados18, '<tbody>', '</tbody>');

	$dom->loadHTML($dados14);
	$Detail4    = $dom->getElementsByTagName('td');
	$categoria  = clearStr($Detail4->item(0)->nodeValue);
	$placa      = clearStr($Detail4->item(1)->nodeValue);
	$chassi     = clearStr($Detail4->item(2)->nodeValue);
	$renavam    = clearStr($Detail4->item(3)->nodeValue);
	$cpfcnpjcar = clearStr($Detail4->item(4)->nodeValue);

	$dom->loadHTML($dados15);
	$Detail5 = $dom->getElementsByTagName('td');
	$cor     = clearStr($Detail5->item(1)->nodeValue);
	$anofab  = clearStr($Detail5->item(2)->nodeValue);
	$anomol  = clearStr($Detail5->item(3)->nodeValue);
	$anoexercicio = clearStr($Detail5->item(4)->nodeValue);
	$marcmodelo   = clearStr($Detail5->item(0)->nodeValue);

	$dom->loadHTML($dados16);
	$Detail6 = $dom->getElementsByTagName('td');
	$municipioEmplacamento  = clearStr($Detail6->item(0)->nodeValue);
	$EstadoEmplacamento     = clearStr($Detail6->item(1)->nodeValue);

	$dom->loadHTML($dados11);
	$Detail = $dom->getElementsByTagName('td');
	$recVec = clearStr($Detail->item(2)->nodeValue);

	$dom->loadHTML($dados18);
	$Detail8 = $dom->getElementsByTagName('td');
	$registroRoubo    		 = clearStr($Detail8->item(1)->nodeValue);
	$sinalizacaoRoubo    	 = clearStr($Detail8->item(2)->nodeValue);
	$sinalizacaoLocalizacao  = clearStr($Detail8->item(3)->nodeValue);
	$ordemJudidicalApreensao = clearStr($Detail8->item(0)->nodeValue);

	return  array(
			'placa'   => $placa,
			'chassi'  => $chassi,
			'renavam' => $renavam,
			'cor'     => $cor,
			'ano_fab' => $anofab,
			'ano_modelo'    => $anomol,
			'marca_modelo'  => $marcmodelo,
			'registro_veiculo' => $recVec,
			'categoria' 	   => $categoria,
			'municipio_emplacamento'   => $municipioEmplacamento,
			'estado_emplacamento'      => $EstadoEmplacamento,
			'ordem_judicial_apreensao' => $ordemJudidicalApreensao,
			'registro_de_roubo'    => $registroRoubo,
			'sinalizacao_de_roubo' => $sinalizacaoRoubo,
			'sinalizacao_de_localizacao' => $sinalizacaoLocalizacao
	);
}

function filtroProprietarioCnpj($dados) {
	$dom 	 = new DOMDocument();

	$dados11 = $dados[1];
	$dados11 = corta($dados11, '<tbody>', '</tbody>');
	$dados12 = $dados[2];
	$dados12 = corta($dados12, '<tbody>', '</tbody>');
	$dados13 = $dados[3];
	$dados13 = corta($dados13, '<tbody>', '</tbody>');
	$dados10 = $dados[10];
	$dados10 = corta($dados10, '<tbody>', '</tbody>');

	$dom->loadHTML($dados11);
	$Detail = $dom->getElementsByTagName('td');
	$mae    = clearStr($Detail->item(1)->nodeValue);

	$dom->loadHTML($dados12);
	$Detail2 = $dom->getElementsByTagName('td');
	$nome    = clearStr($Detail2->item(0)->nodeValue);
	$cpfcnpj = clearStr($Detail2->item(1)->nodeValue);

	$dom->loadHTML($dados13);
	$Detail3 = $dom->getElementsByTagName('td');
	$idade   = clearStr($Detail3->item(1)->nodeValue);
	$signo   = clearStr($Detail3->item(2)->nodeValue);
	$sexo    = clearStr($Detail3->item(3)->nodeValue);
	$situacaodono = clearStr($Detail2->item(4)->nodeValue);
	$nascimento   = clearStr($Detail3->item(0)->nodeValue);

	$dom->loadHTML($dados10);
	$Detail10  = $dom->getElementsByTagName('td');
	$endEstado = clearStr($Detail10->item(0)->nodeValue);
	$endCidade = clearStr($Detail10->item(1)->nodeValue);
	$endBairro = clearStr($Detail10->item(2)->nodeValue);
	$endRua    = clearStr($Detail10->item(3)->nodeValue);
	$endNumero = clearStr($Detail10->item(4)->nodeValue);
	$endCep    = clearStr($Detail10->item(6)->nodeValue);
	$endComplemento = clearStr($Detail10->item(5)->nodeValue);

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
