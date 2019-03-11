<?php

require('lib/util.php');
require('lib/logar.php');
require('lib/veiculo.php');
require('lib/veiculoCpf.php');
require('lib/veiculoCnpj.php');

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

function runPlaca($placa, $usuario, $cookie, $proxy) {
	$placa = strtoupper($placa);
	$placa = xss($placa);
	$dados = lerPlaca($placa);

	if(is_array($dados)) {
		return $dados;
	}else {
		$dados = consultaPlaca($placa, $cookie, $proxy);

		if($dados == 'relogar') {
			return false;
		}elseif($dados == 'nada_encontrado') {
			return 'nada_encontrado';
		}elseif($dados == false) {
			if(check($cookie, $proxy) === false) {
				return false;
			}else{
				$dados = consultaPlaca($placa, $cookie, $proxy);
			}
		}elseif($dados == 'reload') {
			$dados = consultaPlaca($placa, $cookie, $proxy);
		}

		if($dados == 'nada_encontrado') {
			return 'nada_encontrado';
		}

		if(!isset($dados)) {
			return false;
		}

		if(!stristr($dados, 'Renavam')) {
			return array('msg' => 'nada encontrado');
		}

		if(stristr($dados, 'GISTRO EM CNPJ JURIDICO')) {
			contarUsado($usuario, 'vec');
			$retorno = filtroCnpj($dados);
		}else{
			contarUsado($usuario, 'vec');
			$retorno = filtroCpf($dados);
		}

		if(is_array($retorno['veiculo'])) {
			savePlaca($retorno, $placa);
			return $retorno;
		}else{
			return false;
		}
	}
}

function execPlaca($placa) {
	$dados   = ler();

	foreach($dados as $d) {
		$dadosok = $d;
		$usuario = $dadosok['usuario'];
		$senha   = $dadosok['senha'];
		$proxy   = $dadosok['proxy'];
		$cookie  = $dadosok['cookie'];
		$status  = (int)$dadosok['status_vec'];
		$update  = $dadosok['update'];

		$limite_vec  = (int)$dadosok['limite_vec'];
		$usado_vec   = (int)$dadosok['usado_vec'];

		// if($status !== 1) {
		// 	die('1');
		// 	continue;
		// }

		if($usado_vec >= $limite_vec) {
			continue;
		}
		$result = runPlaca($placa, $usuario, $cookie, $proxy);

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
	$placa = xss($_REQUEST['dados']);
    if(strlen($placa) < 3) {
        $error = array('msg' => 'placa_invalida');
    }elseif(strlen($placa) > 10) {
        $error = array('msg' => 'doc_invalido');
    } else {
        $error = null;
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
		$dados = execPlaca($placa);

		if($dados == 'error') {
			$dados = array('msg' => 'nada_encontrado');
		}
	}
}

header("Content-type:application/json");

echo json_encode($dados);
die;




