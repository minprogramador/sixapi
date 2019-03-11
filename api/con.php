<?php

require('lib/util.php');
require('lib/logar.php');
require('lib/condutor.php');
require('lib/filtro.php');
//fazer LOCK mudando o status para 3
//quando voltar ao normal = 1
//desativado = 2


//$v = checkPontos($cookie, $proxy);

function checkLg($dados) {
	$usuario = $dados['usuario'];
	$senha   = $dados['senha'];
	$proxy   = $dados['proxy'];
	$update  = $dados['update'];
	$cookie  = $dados['cookie'];
	$status  = $dados['status'];
	$limite  = $dados['limite'];
	$usado   = $dados['usado'];

	if($usado >= $limite) {
		$error = true;
	}elseif($status === 2) {
		$error = true;
	}elseif($status === 3) {
		$error = true;
	}elseif(strlen($cookie) < 10) {
		$error = true;
	}else{
		$error = false;
	}
	return $error;
}

function runCon($doc, $usuario, $cookie, $proxy) {
	$doc   = xss($doc);
	$dados = lerPlaca($doc);

	if(is_array($dados)) {
		return $dados;
	}else {
		$dados = consultaCondutor($doc, $cookie, $proxy);

		if($dados == 'relogar') {
			return false;
		}elseif($dados == 'nada_encontrado') {
			return 'nada_encontrado';
		}elseif($dados == false) {
			if(check($cookie, $proxy) === false) {
				return false;
			}else{
				$dados = consultaCondutor($doc, $cookie, $proxy);
			}
		}elseif($dados == 'reload') {
			$dados = consultaCondutor($doc, $cookie, $proxy);
		}

		if($dados == 'nada_encontrado') {
			return 'nada_encontrado';
		}
		
		if(!isset($dados)) {
			return false;
		}

		if(!stristr($dados, 'CNH')) {
			return array('msg' => 'nada encontrado');
		}
		contarUsado($usuario, 'con');
		$retorno = filtro($dados);

		if(is_array($retorno['dados'])) {
			savePlaca($retorno, $doc);
			return $retorno;
		}else{
			return false;
		}
	}
}

function execCon($doc) {
	$dados   = ler();

	foreach($dados as $d) {
		$dadosok = $d;
		$usuario = $dadosok['usuario'];
		$senha   = $dadosok['senha'];
		$proxy   = $dadosok['proxy'];
		$cookie  = $dadosok['cookie'];
		$status  = $dadosok['status_con'];
		$update  = $dadosok['update'];

		$limite_con = (int)$dadosok['limite_con'];
		$usado_con  = (int)$dadosok['usado_con'];

		if($usado_con >= $limite_con) {
			continue;
		}

		$result = runCon($doc, $usuario, $cookie, $proxy);
		if($result == 'nada_encontrado') {
			$dok = array('msg' => 'nada_encontrado');
		}elseif($result === false) {
			continue;
		}else{
			$dok = $result;
			break;
		}
	}

	if(isset($dok)) {
		return $dok;
	}else{
		return 'error';
	}

}

$tokenOk = 'beb99570f3a3f81cf289a586e5abd89b';

if(isset($_REQUEST['dados'])) {
	$doc = xss($_REQUEST['dados']);
    $doc = str_replace(['.', '-', ' ', '/', '	'], '', $doc);
    if(isset($doc)) {
        if(!preg_match("#^([0-9]){3}([0-9]){3}([0-9]){3}([0-9]){2}$#i", $doc)) {
	        $error = array('msg' => 'doc_invalido');
        }else{
 			$error = null;
        }
    }
} else {
    $error = array('msg' => false);
}

if($_REQUEST['token'] != $tokenOk) {
	$dados = array('msg' => 'acesso invalido');
} else {
	if(isset($error)) {
		$dados = $error;
	}else{
		$dados = execCon($doc);

		if($dados == 'error') {
			$dados = array('msg' => 'nada_encontrado');
		}
	}
}

header("Content-type:application/json");

echo json_encode($dados);
die;




