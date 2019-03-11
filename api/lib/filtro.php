<?php

function filtro($res) {
	if(!stristr($res, 'me do Condutor</td>')) {
		return array('msg' => 'nada encontrado');
	}

	$dados = explode('<table width="100%" class="tabelaHs">', $res);
	$retorno = filtromin($dados);

	$retorno = array(
		'dados' => $retorno
	);

	return $retorno;
}



function filtromin($dados) {
	$dom = new DOMDocument();

	$dados1 = $dados[1];
	$dados1 = corta($dados1, '<tbody>', '</tbody>');

	$dom->loadHTML($dados1);
	$Detail      = $dom->getElementsByTagName('td');

	$nome        = clearStr($Detail->item(0)->nodeValue);
	$categoria   = clearStr($Detail->item(1)->nodeValue);
	$situacaocnh = clearStr($Detail->item(2)->nodeValue);
	$ufcnh 		 = clearStr($Detail->item(3)->nodeValue);

	$dados2 = $dados[2];
	$dados2 = corta($dados2, '<tbody>', '</tbody>');

	$dom->loadHTML($dados2);
	$Detail = $dom->getElementsByTagName('td');
	$doc    = clearStr($Detail->item(0)->nodeValue);
	$cnh    = clearStr($Detail->item(1)->nodeValue);
	$rg     = clearStr($Detail->item(2)->nodeValue);

	$dados3 = $dados[3];
	$dados3 = corta($dados3, '<tbody>', '</tbody>');

	$dom->loadHTML($dados3);
	$Detail  = $dom->getElementsByTagName('td');
	$priHab  = clearStr($Detail->item(0)->nodeValue);
	$vencCnh = clearStr($Detail->item(1)->nodeValue);

	$dados4  = $dados[4];
	$dados4  = corta($dados4, '<tbody>', '</tbody>');

	@$dom->loadHTML($dados4);
	$Detail = $dom->getElementsByTagName('td');
	$nomerc = clearStr($Detail->item(0)->nodeValue);
	$maerc  = clearStr($Detail->item(1)->nodeValue);
	$nasc   = clearStr($Detail->item(2)->nodeValue);
	$idade  = clearStr($Detail->item(3)->nodeValue);
	$signo  = clearStr($Detail->item(4)->nodeValue);
	$sexo   = clearStr($Detail->item(5)->nodeValue);
	$situacaocpf = clearStr($Detail->item(6)->nodeValue);


	$dados5  = $dados[5];
	$dados5  = corta($dados5, '<tbody>', '</tbody>');

	@$dom->loadHTML($dados5);
	$Detail      = $dom->getElementsByTagName('td');
	$end_uf      = clearStr($Detail->item(0)->nodeValue);
	$end_cidade  = clearStr($Detail->item(1)->nodeValue);
	$end_rua     = clearStr($Detail->item(2)->nodeValue);
	$end_numero  = clearStr($Detail->item(3)->nodeValue);
	$end_bairro  = clearStr($Detail->item(4)->nodeValue);
	$end_comple  = clearStr($Detail->item(5)->nodeValue);
	$end_cep     = clearStr($Detail->item(6)->nodeValue);


	$retorno = array(
		'doc'   => $doc,
		'nome'  => $nome,
		'mae'   => $maerc,
		'nascimento' => $nasc,
		'idade'   => $idade,
		'signo'   => $signo,
		'sexo'    => $sexo,
		'sit_cpf' => $situacaocpf,
		'n_rg'    => $rg,
		'n_cnh'   => $cnh,
		'cat_cnh' => $categoria,
		'ufcnh'   => $ufcnh,
		'priHab'  => $priHab,
		'vencCnh' => $vencCnh,
		'sit_cnh' => $situacaocnh,
		'endereco' => array(
			'logradouro'  => $end_rua,
			'numero'      => $end_numero,
			'bairro'      => $end_bairro,
			'complemento' => $end_comple,
			'cep'         => $end_cep,
			'cidade' 	  => $end_cidade,
			'uf' 		  => $end_uf
		)
	);
	return $retorno;
}
